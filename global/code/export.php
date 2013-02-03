<?php

/*------------------------------------------------------------------------------------------------*\
  Copyright (C) 2010  Benjamin Keen
  http://www.opentranslate.org

	The new (v2) export mechanism exports all translation files for the 

	/project_summary.php
	/v1_0_1/summary.php
	/v1_0_1/... language files

	/v1_0_2/summary.php
	/v1_0_2/...language files
\*------------------------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------------------------*\
  EXPORT - functions in brief

    ot_generate_php_language_file
    ot_generate_php_project_version_summary_file
    ot_get_version_export_settings
    ot_get_version_language_export_settings
    ot_update_version_php_export_settings
    ot_update_version_language_php_export_settings
    ot_send_project_version_via_ftp
    ot_test_ftp_settings
    ot_get_exported_language_file_translation_last_modified_date
    ot_update_language_file_last_modified_date

\*------------------------------------------------------------------------------------------------*/


/**
 * Generates a PHP language file for a particular project-version-language.
 */
function ot_generate_php_language_file($version_id, $language_id)
{
  global $g_root_dir;

	// get all the data and translations
  
	// find the base version
	$base_version_id = ot_get_base_version($version_id);

	// if we're just searching a base version, sweet! The query is nice and simple.
	$return_hash = array();
	if ($base_version_id == $version_id)
	{
    $query = mysql_query("
        SELECT *, d.data_id as curr_data_id
        FROM   tr_data d
          INNER JOIN tr_project_categories pvc ON d.category_id = pvc.category_id
          LEFT JOIN tr_data_translations dt ON d.data_id = dt.data_id AND dt.language_id = $language_id
        WHERE  d.version_id = $version_id
			  ORDER BY pvc.category_order, d.data_category_order
                  ");
	}
	else
	{
    $version_chain = ot_get_parent_versions($version_id);
    $version_chain_str = join(", ", $version_chain);
  
    array_shift($version_chain);  // remove the base version
    $version_chain_str_omit_base_version = join(", ", $version_chain);
	
    $query = mysql_query("
      SELECT *, d.data_id as curr_data_id
      FROM   tr_data d
        INNER JOIN tr_project_categories pvc ON d.category_id = pvc.category_id
        LEFT JOIN tr_data_translations dt ON d.data_id = dt.data_id AND dt.language_id = $language_id
      WHERE  d.version_id IN ($version_chain_str) AND
        d.data_id NOT IN (SELECT data_id FROM tr_data_version_changes WHERE version_id IN ($version_chain_str_omit_base_version)) AND
        d.data_id NOT IN (SELECT data_id FROM tr_data_version_deletions WHERE version_id IN ($version_chain_str_omit_base_version))
			ORDER BY pvc.category_order, d.data_category_order
            ");
	}

  // get the version's general export settings
  $version_export_settings  = ot_get_version_export_settings($version_id);
  $php_comments_header      = $version_export_settings["php_comments_header"];
  $php_translation_var_name = $version_export_settings["php_translation_var_name"];

  // replace any placeholders in the comments header (%%translators%% and %%creationdate%%)
  $curr_datetime = date("M jS, g:i A");
  $php_comments_header = preg_replace("/%%creationdate%%/", $curr_datetime, $php_comments_header);

  // find out which translators for this project-language want to be credited for their work
  $translators_to_credit = ot_get_translators_for_credit($version_id, $language_id);

  $translator_str = "";
  if (empty($translators_to_credit))
    $translator_str = "Anonymous";
  else
  {
    $translator_names = array();
    foreach ($translators_to_credit as $translator)
      $translator_names[] = "{$translator["first_name"]} {$translator["last_name"]}";

    $translator_str = join(", ", $translator_names);
  }
  $php_comments_header = preg_replace("/%%translators%%/", $translator_str, $php_comments_header);


  // construct the PHP file content. This stores all the translations in $php_file, grouped by categories
  $last_category = "";
  $php_file = array();

  while ($row = mysql_fetch_assoc($query))
  {
    if ($last_category != $row["category_name"])
    {
      if (!empty($last_category))
      {
        $php_file[] = "";
        $php_file[] = "";
      }
      $last_category = $row["category_name"];
    }
    $php_label = $row["data_label"];
    $translation = isset($row["translation"]) ? $row["translation"] : $row["data"];
    $translation = ot_clean_for_double_quoted_str($translation);

		$str = '$' . $php_translation_var_name . '["' . $php_label . '"] = "' . $translation . '";';
		$php_file[$row["category_name"]][] = $str;
  }
	
  // now generate the file string and ordered by category, then hash key. Note that
	// we explictly define the array as an array. For some reason, if we don't it causes problems
	// on some servers
  $php_file_rows = array($php_comments_header, "\${$php_translation_var_name} = array();\n");
  while (list($category, $translations) = each($php_file))
  {
    if (empty($translations))
      $php_file_rows[] = "";

    if (is_array($translations))
    {
      $php_file_rows[] = "// Category: $category";
      sort($translations);

      $php_file_rows = array_merge($php_file_rows, $translations);
    }
  }

  // get the export settings
  $vl_info = ot_get_version_language_info($version_id, $language_id);
  $filename = $vl_info["php_filename"];
  $file = "$g_root_dir/tmp/$filename";
  $file_str = "<". "?php\n\n" . join("\n", $php_file_rows) . "\n\n?" . ">";

  return $file_str;
}


/**
 * Generates the main project summary file.
 */
function ot_generate_php_project_summary_file($project_id)
{
  $export_time = date("U");

  $project_info = ot_get_project($project_id);
  $project_name = $project_info["name"];
  $project_last_modified = $project_info["last_modified"];
  $project_trust_threshold = $project_info["trust_threshold"];
  $translator_blacklist_threshold = $project_info["translator_blacklist_threshold"];

  $languages = ot_get_project_languages($project_id);
	
  $num_languages = count($languages);
	$language_names = array();
	foreach ($languages as $language_info)
	{
	  $language_names[] = "\"{$language_info["language_name"]}\"";
	}
	$language_name_str = join(", ", $language_names);
	
  $versions = ot_get_project_versions($project_id);
	$num_versions = count($versions);
	
	// this SUCKS. Originally, the languages were associated with versions; then it was simplified
	// to associate them with the project. The tr_project_version_language_stats table 
  $hash = ot_get_filename_langname_hash($versions[0]["version_id"]);
	$lang_rows = array();
	while (list($filename, $language) = each($hash))
	  $lang_rows[] = "\"$filename\" => \"$language\"";
	
	$language_hash_str = implode(", ", $lang_rows);;
	
	$export_version_info = array();
	foreach ($versions as $version_info)
	{
	  $info = array(
      "version_id" => $version_info["version_id"],
      "version_label" => $version_info["version_label"],
      "parent_version_id" => $version_info["parent_version_id"],
      "date_created" => $version_info["date_created"],
      "last_modified" => $version_info["last_modified"],
			"export_folder" => $version_info["export_folder"]			
			  );

	  $export_version_info["version_{$version_info["version_id"]}"] = $info; 
	}
	
	$version_array = var_export($export_version_info, true);

  $file_str = "<" . "?php

\$OT_info = array();
\$OT_info[\"export_time\"] = $export_time;

// project information
\$OT_project = array();
\$OT_project[\"project_name\"] = \"$project_name\";
\$OT_project[\"project_last_updated\"] = \"$project_last_modified\";
\$OT_project[\"project_trust_threshold\"] = $project_trust_threshold;
\$OT_project[\"translator_blacklist_threshold\"] = $translator_blacklist_threshold;

// ongoing translations
\$OT_project[\"num_languages\"] = $num_languages;
\$OT_project[\"languages\"] = array($language_name_str);
\$OT_project[\"language_hash\"] = array($language_hash_str);

// versions
\$OT_project[\"num_versions\"] = $num_versions;
\$OT_project[\"versions\"] = $version_array;
";

  return $file_str;
}


/**
 * Generates the PHP summary file content for a particular project version.
 */
function ot_generate_php_project_version_summary_file($version_id)
{
  $project_info = ot_get_project_from_version_id($version_id);
	$project_name = $project_info["name"];
  $project_last_modified = $project_info["last_modified"];
  $project_trust_threshold = $project_info["trust_threshold"];
  $translator_blacklist_threshold = $project_info["translator_blacklist_threshold"];
  $version_info = ot_get_project_version($version_id);
  $version_label = $version_info["version_label"];
  $version_last_modified = $version_info["last_modified"];

  $version_stats = ot_get_project_version_statistics($version_id);
	
  $OT_translations = array();
  while ($vl_stats = mysql_fetch_assoc($version_stats))
  {
    // only add the translation language info it's marked as "Complete" (i.e. ready for export)
		// this can be enabled/disabled on a per-version basis
    if ($vl_stats["php_export_status"] == "Incomplete")
      continue;
			
		$language_id = $vl_stats["language_id"]; 

	  $translators_to_credit = ot_get_translators_for_credit($version_id, $language_id);
		$names = array();
		foreach ($translators_to_credit as $translator_info)
		{
		  $names[] = ot_clean_hash("{$translator_info["first_name"]} {$translator_info["last_name"]}");
		}

    $OT_translations[$language_id] = array(
        "language_name" => $vl_stats["language_name"],
        "php_export_status" => $vl_stats["php_export_status"],
        "php_filename" => $vl_stats["php_filename"],
        "percent_reliability" => $vl_stats["percent_reliability"],
        "percent_translated" => $vl_stats["percent_translated"],
				"percent_unreviewed_auto_translations" => ot_get_percentage_unreviewed_auto_translations($version_id, $language_id),
				"num_unreviewed_auto_translations" => ot_get_unreviewed_auto_translations($version_id, $language_id),
				"translators" => $names
          );
  }
	
  $translation_array = var_export($OT_translations, true);
  $export_time = date("U");

  $file_str = "<" . "?php

\$OT_info = array();
\$OT_info[\"export_time\"] = $export_time;

// version information
\$OT_version = array();
\$OT_version[\"version_id\"] = $version_id;
\$OT_version[\"version_label\"] = \"$version_label\";
\$OT_version[\"version_last_modified\"] = \"$version_last_modified\";
\$OT_version[\"version_last_modified\"] = \"$version_last_modified\";

// translation information
\$OT_translations = $translation_array;

?" . ">";

  return $file_str;
}


/**
 * Returns export settings that are COMMON TO ALL LANGUAGES WITHIN A PROJECT VERSION. If the record
 * doesn't exist in the tr_project_version_export_settings table, it creates a blank row and returns
 * the empty values.
 */
function ot_get_version_export_settings($version_id)
{
  $result = mysql_query("
    SELECT *
    FROM   tr_project_version_export_settings
    WHERE  version_id = $version_id
      ");

  $num_rows = mysql_num_rows($result);
  if ($num_rows == 0)
  {
    mysql_query("INSERT INTO tr_project_version_export_settings (version_id) VALUES ($version_id)");

    $result = mysql_query("
      SELECT *
      FROM   tr_project_version_export_settings
      WHERE  version_id = $version_id
        ");
  }

  return mysql_fetch_assoc($result);
}


/**
 * Updates the export settings for a particular project version.
 */
function ot_update_version_php_export_settings($version_id, $info)
{
  $info = ot_clean_hash($info);
  $php_comments_header      = $info["php_comments_header"];
  $php_translation_var_name = $info["php_translation_var_name"];

  $result = mysql_query("
    UPDATE tr_project_version_export_settings
    SET    php_comments_header = '$php_comments_header',
           php_translation_var_name = '$php_translation_var_name'
    WHERE  version_id = $version_id
      ");

  return array(true, "Your export settings have been updated.");
}


/**
 * Updates the PHP export settings (i.e. the PHP filename) for a particular project-language.
 */
function ot_update_version_language_php_export_settings($version_id, $language_id, $filename)
{
  $php_export_status = empty($filename) ? "Incomplete" : "Complete";

  $result = mysql_query("
    UPDATE tr_project_version_language_stats
    SET    php_filename = '$filename',
           php_export_status = '$php_export_status'
    WHERE  version_id = $version_id AND
           language_id = $language_id
             ") or die(mysql_error());

  return array(true, "The PHP export file settings for this language have been updated.");
}


function ot_set_project_ftp_settings_confirmed($project_id, $is_confirmed)
{
  $ftp_settings_confirmed = ($is_confirmed) ? "yes" : "no";

  $result = mysql_query("
    UPDATE tr_projects
    SET    ftp_settings_confirmed = '$ftp_settings_confirmed'
    WHERE  project_id = $project_id
             ") or die(mysql_error());
}


/**
 * This exports an entire project via FTP.
 */
function ot_send_project_via_ftp($project_id)
{
  global $g_root_dir, $g_root_url;
 
  ot_send_project_summary_file_via_ftp($project_id);

	$project_versions = ot_get_project_versions($project_id);	
	foreach ($project_versions as $version_info)
	{
	  $version_id = $version_info["version_id"];
		ot_send_project_version_via_ftp($version_id);
	}
}


function ot_send_project_summary_file_via_ftp($project_id)
{
  global $g_root_dir;
	
	$project = ot_get_project($project_id);
  $ftp_hostname    = $project["ftp_hostname"];
  $ftp_site_folder = $project["ftp_site_folder"];
  $ftp_username    = $project["ftp_username"];
  $ftp_password    = $project["ftp_password"];

	$connection_id = ftp_connect($ftp_hostname);
	$login_result  = ftp_login($connection_id, $ftp_username, $ftp_password);

  // generate and export the project summary file
  $summary_file = ot_generate_php_project_summary_file($project_id);
	$fh = fopen("$g_root_dir/tmp/project_summary.php", "w");
	fwrite($fh, $summary_file);
	fclose($fh);
  $source_file = "$g_root_dir/tmp/project_summary.php";
  $destination_file = "$ftp_site_folder/project_summary.php";
	$upload = @ftp_put($connection_id, $destination_file, $source_file, FTP_ASCII);
  @unlink("$g_root_dir/tmp/project_summary.php");

	@ftp_close($connection_id);

	if (!$upload)
    return array(false, "We were unable to upload a file to that folder. Please double-check the FTP Site Folder setting.");
}


/**
 * A basic FTP function for version 1. This simply sends each language file (including the original)
 * to the target FTP site & location. It sends the file one by one, which is grossly inefficient.
 *
 * @param array $export_info - an array of language IDs and "summary" if the user wants to send the
 *          summary text file.
 */
function ot_send_project_version_via_ftp_old($version_id, $export_info = array())
{
  global $g_root_dir;

  // get the FTP settings for the project
  $project      = ot_get_project_from_version_id($version_id);
  $project_id   = $project["project_id"];
	$version_info = ot_get_project_version($version_id);

  if ($project["ftp_settings_confirmed"] == "no")
    return array(false, "Sorry, this project hasn't had the FTP information fully configured. Please "
      . "return to your Project Settings page, enter the FTP information and click the \"Test FTP Settings\" button.");

  $ftp_hostname    = $project["ftp_hostname"];
  $ftp_site_folder = $project["ftp_site_folder"];
  $ftp_username    = $project["ftp_username"];
  $ftp_password    = $project["ftp_password"];

	$connection_id = ftp_connect($ftp_hostname);
	$login_result  = ftp_login($connection_id, $ftp_username, $ftp_password);

	// check connection
	if (!$connection_id || !$login_result)
    return array(false, "Sorry, this FTP connection failed.");
	
	$version_subfolder = $version_info["export_folder"]; 
	
  // 1. generate and export the project version translation summary file. Only export this language
  // if it's included in the export list (or the list if empty, which indicates *everything*)
  $summary_file = ot_generate_php_project_version_summary_file($version_id);
	$fh = fopen("$g_root_dir/tmp/summary.php", "w");
	fwrite($fh, $summary_file);
	fclose($fh);
  $source_file = "$g_root_dir/tmp/summary.php";
  $destination_file = "$ftp_site_folder/$version_subfolder/summary.php";
	$upload = @ftp_put($connection_id, $destination_file, $source_file, FTP_ASCII);
  @unlink("$g_root_dir/tmp/summary.php");

	if (!$upload)
    return array(false, "We were unable to upload a file to that folder. Please double-check the FTP Site Folder setting.");

  // 2. generate, zip up and export the language file(s) - including the original language file - and the project
	// version summary file(s)
  $statistics_query = ot_get_project_version_statistics($version_id);
	$exported_languages = array();
  while ($version_lang = mysql_fetch_assoc($statistics_query))
  {
    $language_id  = $version_lang["language_id"];
    $php_filename = $version_lang["php_filename"];
    $php_export_status = $version_lang["php_export_status"];

    if ($php_export_status != "Complete")
      continue;

		// FIXME bug here, by the looks of it

    // get the datetime of the most recently added/updated translation in this language
    $language_last_modified_date          = ot_get_version_language_last_modified_date($version_id, $language_id);
    $language_last_modified_date_unixtime = ot_convert_datetime_to_timestamp($language_last_modified_date);

    // now find the datetime of the most recently added/updated translation, in the PREVIOUS export file
    $previous_export_translation_last_modified_date = ot_get_exported_language_file_translation_last_modified_date($project_id, $version_id, $language_id);
    $previous_export_translation_last_modified_unixtime = "";
    if (!empty($previous_export_translation_last_modified_date))
      $previous_export_translation_last_modified_unixtime = ot_convert_datetime_to_timestamp($previous_export_translation_last_modified_date);

    // if the language has (a) never been exported or (b) been updated since the last export, export the sucker
		$never_exported   = (empty($previous_export_translation_last_modified_unixtime)) ? true : false;
		$recently_updated = (!empty($previous_export_translation_last_modified_unixtime) && $language_last_modified_date_unixtime > $previous_export_translation_last_modified_unixtime) ? true : false;

    if ($never_exported || $recently_updated)
    {
      // only export this language if it's included in the export list (or the list if empty == everything)
      if (!empty($export_info) && !in_array($language_id, $export_info))
        continue;

		  $exported_languages[] = $version_lang["language_name"];

      // generate the file content
      $file_content = ot_generate_php_language_file($version_id, $language_id);

      // zip up the language file into a zip with the same name (but .zip extension)
      $php_filename_no_ext = substr($php_filename, 0, strrpos($php_filename, '.'));
			$php_filename_and_path = "$g_root_dir/tmp/$php_filename";
      $zip_filename = "{$php_filename_no_ext}.zip";
      $zip_filename_and_path = "$g_root_dir/tmp/$zip_filename";

			// remove the old zipfile and PHP file if either exists
			@unlink($zip_filename_and_path);
  		@unlink("$g_root_dir/tmp/$php_filename");

  		$zip = new createZip();
			$zip->addFile($file_content, $php_filename);

			$fd = fopen($zip_filename_and_path, "wb");
			$out = fwrite($fd, $zip->getZippedfile()); // utf8_encode
			fclose($fd);

			// FTP the unzipped PHP file as well (added for the Custom Build script to have an unzipped version available)
      $destination_file = "$ftp_site_folder/$version_subfolder/$php_filename";
    	$upload = ftp_put($connection_id, $destination_file, $php_filename_and_path, FTP_BINARY);
      chmod($php_filename_and_path, 0777);

      $destination_file = "$ftp_site_folder/$version_subfolder/$zip_filename";
    	$upload = ftp_put($connection_id, $destination_file, $zip_filename_and_path, FTP_BINARY);
      chmod($zip_filename_and_path, 0777);

			// if the upload was unsuccessful, inform the user. Otherwise, update the export log
    	if (!$upload)
        return array(false, "We were unable to upload a file to that folder. Please double-check the FTP Site Folder setting.");
      else
        ot_update_export_log($project_id, $version_id, $language_id, $language_last_modified_date);
    }
  }

  // close the FTP stream
	@ftp_close($connection_id);

	$message = "";
	if (!empty($exported_languages))
   	$message = "The FTP export to <b>$ftp_hostname</b> was successful. The updated language files in this export were:  " . join(", ", $exported_languages);
	else
    $message = "The FTP export to <b>$ftp_hostname</b> was successful. None of the language files have changed since the last export so only the summary file was updated.";

  return array(true, $message);
}


/**
 * A basic FTP function for version 1. This simply sends each language file (including the original)
 * to the target FTP site & location. It sends the file one by one, which is grossly inefficient.
 *
 * @param array $export_info - an array of language IDs and "summary" if the user wants to send the
 *          summary text file.
 */
function ot_send_project_version_via_ftp($version_id, $export_info = array())
{
  global $g_root_dir;

  // get the FTP settings for the project
  $project      = ot_get_project_from_version_id($version_id);
  $project_id   = $project["project_id"];
	$version_info = ot_get_project_version($version_id);

  if ($project["ftp_settings_confirmed"] == "no")
    return array(false, "Sorry, this project hasn't had the FTP information fully configured. Please "
      . "return to your Project Settings page, enter the FTP information and click the \"Test FTP Settings\" button.");

  $ftp_hostname    = $project["ftp_hostname"];
  $ftp_site_folder = $project["ftp_site_folder"];
  $ftp_username    = $project["ftp_username"];
  $ftp_password    = $project["ftp_password"];

	$connection_id = ftp_connect($ftp_hostname);
	$login_result  = ftp_login($connection_id, $ftp_username, $ftp_password);

	// check connection
	if (!$connection_id || !$login_result)
    return array(false, "Sorry, this FTP connection failed.");
	
	$version_subfolder = $version_info["export_folder"]; 
	
  // 1. generate and export the project version translation summary file. Only export this language
  // if it's included in the export list (or the list if empty, which indicates *everything*)
  $summary_file = ot_generate_php_project_version_summary_file($version_id);
	$fh = fopen("$g_root_dir/tmp/summary.php", "w");
	fwrite($fh, $summary_file);
	fclose($fh);
  $source_file = "$g_root_dir/tmp/summary.php";
  $destination_file = "$ftp_site_folder/$version_subfolder/summary.php";
	$upload = @ftp_put($connection_id, $destination_file, $source_file, FTP_ASCII);
  @unlink("$g_root_dir/tmp/summary.php");
	
	if (!$upload)
    return array(false, "We were unable to upload a file to that folder. Please double-check the FTP Site Folder setting.");

  // 2. generate, zip up and export the language file(s) - including the original language file - and the project
	// version summary file(s)
  $statistics_query = ot_get_project_version_statistics($version_id);
	$exported_languages = array();
  while ($version_lang = mysql_fetch_assoc($statistics_query))
  {
    $language_id  = $version_lang["language_id"];
    $php_filename = $version_lang["php_filename"];
    $php_export_status = $version_lang["php_export_status"];

    if ($php_export_status != "Complete")
      continue;

    // get the datetime of the most recently added/updated translation in this language
    $language_last_modified_date          = ot_get_version_language_last_modified_date($version_id, $language_id);
    $language_last_modified_date_unixtime = ot_convert_datetime_to_timestamp($language_last_modified_date);

    // now find the datetime of the most recently added/updated translation, in the PREVIOUS export file
    $previous_export_translation_last_modified_date = ot_get_exported_language_file_translation_last_modified_date($project_id, $version_id, $language_id);
    $previous_export_translation_last_modified_unixtime = "";
    if (!empty($previous_export_translation_last_modified_date))
      $previous_export_translation_last_modified_unixtime = ot_convert_datetime_to_timestamp($previous_export_translation_last_modified_date);

    // if the language has (a) never been exported or (b) been updated since the last export, export the sucker
		$never_exported   = (empty($previous_export_translation_last_modified_unixtime)) ? true : false;
		$recently_updated = (!empty($previous_export_translation_last_modified_unixtime) && $language_last_modified_date_unixtime > $previous_export_translation_last_modified_unixtime) ? true : false;

    if ($never_exported || $recently_updated)
    {
      // only export this language if it's included in the export list (or the list if empty == everything)
      if (!empty($export_info) && !in_array($language_id, $export_info))
        continue;

		  $exported_languages[] = $version_lang["language_name"];

      // generate the file content
      $file_content = ot_generate_php_language_file($version_id, $language_id);

			// create the file 
			
      // zip up the language file into a zip with the same name (but .zip extension)
      $php_filename_no_ext = substr($php_filename, 0, strrpos($php_filename, '.'));
      $zip_filename = "{$php_filename_no_ext}.zip";
      $zip_filename_and_path = "$g_root_dir/tmp/$zip_filename";

			// remove the old zipfile and PHP file if either exists
			@unlink($zip_filename_and_path);
  		@unlink("$g_root_dir/tmp/$php_filename");

			// create the file
      $fh = fopen("$g_root_dir/tmp/$php_filename", "w");
      fwrite($fh, $file_content);
      fclose($fh);

			// FTP the unzipped PHP file as well (added for the Custom Build script to have an unzipped version available)
      $destination_file = "$ftp_site_folder/$version_subfolder/$php_filename";
    	$upload = ftp_put($connection_id, $destination_file, "$g_root_dir/tmp/$php_filename", FTP_BINARY);
      chmod("$g_root_dir/tmp/$php_filename", 0777);

			// create the zip archive
      $zip = new PclZip("$g_root_dir/tmp/$zip_filename");
      $zip->add("$g_root_dir/tmp/$php_filename", PCLZIP_OPT_REMOVE_PATH, "$g_root_dir/tmp");

      $destination_file = "$ftp_site_folder/$version_subfolder/$zip_filename";
    	$upload = ftp_put($connection_id, $destination_file, $zip_filename_and_path, FTP_BINARY);
      chmod($zip_filename_and_path, 0777);

			// if the upload was unsuccessful, inform the user. Otherwise, update the export log
    	if (!$upload)
        return array(false, "We were unable to upload a file to that folder. Please double-check the FTP Site Folder setting.");
      else
        ot_update_export_log($project_id, $version_id, $language_id, $language_last_modified_date);
    }
  }
	
  // close the FTP stream
	@ftp_close($connection_id);

	$message = "";
	if (!empty($exported_languages))
   	$message = "The FTP export to <b>$ftp_hostname</b> was successful. The updated language files in this export were:  " . join(", ", $exported_languages);
	else
    $message = "The FTP export to <b>$ftp_hostname</b> was successful. None of the language files have changed since the last export so only the summary file was updated.";

  return array(true, $message);
}


/**
 * Helper function to test FTP settings
 */
function ot_test_ftp_settings($info)
{
  global $g_root_dir, $LANG;

  $ftp_hostname    = $info["ftp_hostname"];
  $ftp_site_folder = $info["ftp_site_folder"];
  $ftp_username    = $info["ftp_username"];
  $ftp_password    = $info["ftp_password"];

	$connection_id = @ftp_connect($ftp_hostname);

  if (!$connection_id)
    return array(false, "Sorry, we couldn't reach the server <b>$ftp_hostname</b>.");

	$login_result = @ftp_login($connection_id, $ftp_username, $ftp_password);

	// check connection
	if (!$login_result)
    return array(false, $LANG["notify_ftp_invalid_username_password"]);

  // now send the file
  $source_file = "$g_root_dir/admin/projects/export/ftp_file_test.txt";
  $destination_file = "$ftp_site_folder/ftp_file_test.txt";
  $destination_file2 = "$ftp_site_folder/test.txt";

	$upload = @ftp_put($connection_id, $destination_file, $source_file, FTP_ASCII);

	if (!$upload)
    return array(false, "We were unable to upload a test file to that folder. Please double-check the FTP Site Folder setting.");

	// close the FTP stream
	@ftp_close($connection_id);

  return array(true, $LANG["notify_ftp_settings_correct"]);
}


/**
 * Apologies for the verbose name, but at least it's clear. This function returns the MySQL datetime
 * of the most recently edited/added item that was exported in the most recent export file for a
 * version-language.
 */
function ot_get_exported_language_file_translation_last_modified_date($project_id, $version_id, $language_id)
{
  $query = mysql_query("
    SELECT translations_last_modified_date
    FROM   tr_logs_export
    WHERE  project_id = $project_id AND
           version_id = $version_id AND
           language_id = $language_id
      ") or die(mysql_error());
  $result = mysql_fetch_assoc($query);

  $last_modified = (!empty($result)) ? $result["translations_last_modified_date"] : "";

  return $last_modified;
}


/**
 * Returns the MySQL datetime of the last updated item in a language file (i.e. a version-language combination).
 * @param string $last_modified - datetime
 */
function ot_update_export_log($project_id, $version_id, $language_id, $last_modified)
{
  $now = date("Y-m-d H:i:s");

  mysql_query("
    DELETE FROM tr_logs_export
    WHERE project_id = $project_id AND
          version_id = $version_id AND
          language_id = $language_id
      ") or die(mysql_error());

  mysql_query("
    INSERT INTO tr_logs_export (project_id, version_id, language_id, export_date, translations_last_modified_date)
    VALUES ($project_id, $version_id, $language_id, '$now', '$last_modified')
      ") or die(mysql_error());
}


/**
 * Returns the last modified date for a version-language.
 */
function ot_get_version_language_last_modified_date($version_id, $language_id)
{
  $query = mysql_query("
		SELECT translation_last_change_date
		FROM   tr_project_version_language_stats
		WHERE  version_id = $version_id AND
		       language_id = $language_id
      ");
  $result = mysql_fetch_assoc($query);

  return $result["translation_last_change_date"];
}


/**
 * Clears the export logs for a particular project version.
 */
function ot_clear_project_version_export_log($project_id, $version_id)
{
  @mysql_query("DELETE FROM tr_logs_export WHERE project_id = $project_id AND version_id = $version_id");
}


function ot_get_filename_langname_hash($version_id)
{
  $query = mysql_query("
    SELECT s.php_filename, l.language_name
    FROM   tr_project_version_language_stats s, tr_languages l
    WHERE  l.language_id = s.language_id AND
           version_id = $version_id
      ");
  $info = array();
	while ($row = mysql_fetch_assoc($query))
	{
	  $info[$row["php_filename"]] = $row["language_name"]; 
	}
	
	return $info;
}
