<?php

/*------------------------------------------------------------------------------------------------*\
  Copyright (C) 2010 Benjamin Keen
  http://www.opentranslate.org
\*------------------------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------------------------*\
  PROJECTS - functions in brief

    ot_add_project
    ot_get_project
    ot_get_project_from_version_id
    ot_get_project_id_from_version_id
    ot_get_projects
    ot_get_project_languages
    ot_delete_project
    ot_update_project_settings
    ot_update_project_description
    ot_update_project_translator_notes

\*------------------------------------------------------------------------------------------------*/


/**
 * Called by superuser. Creates a fresh project with a single "original" version. No validation
 * since it's just for me for the present.
 */
function ot_add_project()
{
  $project_name = $_POST['project_name'];
  $user_type    = $_POST['user_type'];
  $project_leader_first_name = (isset($_POST['project_leader_first_name']) && !empty($_POST["project_leader_first_name"])) ? $_POST["project_leader_first_name"] : "";
  $project_leader_last_name  = (isset($_POST['project_leader_last_name']) && !empty($_POST["project_leader_last_name"])) ? $_POST["project_leader_last_name"] : "";
  $project_leader_email      = (isset($_POST['project_leader_email']) && !empty($_POST["project_leader_email"])) ? $_POST["project_leader_email"] : "";
	$user_type = $_POST['user_type'];
  $project_visibility = $_POST['project_visibility'];
  $origin_language_id = $_POST['origin_language_id'];

  $now = ot_get_current_datetime();

	if ($user_type == "new_user")
  {
    $created_by_account_id = $_SESSION["ot"]["account_id"];
    $password = ot_generate_password();

    $ui_language_id = 25; // ENGLISH

    // first, create the main ACCOUNT record
    $query = mysql_query("
  		INSERT INTO tr_accounts (account_type, status, date_created,
  			created_by_account_id, last_modified, last_logged_in, first_name, last_name, email,
  			password, ui_language_id)
  		VALUES ('project_manager', 'active', '$now', '$created_by_account_id', '$now',
  			'', '$project_leader_first_name', '$project_leader_last_name', '$project_leader_email', '$password', '$ui_language_id')
        ");

    $account_id = mysql_insert_id();

    // next, create a corresponding entry in the project_managers table
    $query = mysql_query("
  		INSERT INTO tr_project_managers (project_manager_id, can_create_projects,
  		  can_create_project_manager_accounts, can_create_translator_accounts, can_export_data)
  		VALUES ($account_id, 'no', 'no', 'no', 'no')
        ");
  }
	else
	  $account_id = $_POST['existing_user_id'];


	// create the new blank project
  $query = mysql_query("
    INSERT INTO tr_projects (status, name, creation_date, last_modified, origin_language_id, project_visibility)
    VALUES ('new', '$project_name', '$now', '$now', '$origin_language_id', '$project_visibility')
      ");

  $new_project_id = mysql_insert_id();

  // map it to the project leader
  $query = mysql_query("
    INSERT INTO tr_project_manager_projects (account_id, project_id)
    VALUES ('$account_id', '$new_project_id')
      ");

  // create a default "original" version
  $query = mysql_query("
    INSERT INTO tr_project_versions (project_id, date_created, last_modified, may_translate, is_visible)
    VALUES ('$new_project_id', '$now', '$now', 'no', 'no')
      ");

	return $new_project_id;
}


/**
 * To retrieve all information about a project in a HASH.
 */
function ot_get_project($project_id)
{
  $project_query = mysql_query("
    SELECT *
		FROM   tr_projects
		WHERE  project_id = $project_id
	    ");
  $project_manager_query = mysql_query("
    SELECT a.account_id, a.first_name, a.last_name
		FROM   tr_projects p, tr_project_manager_projects pmp, tr_accounts a
		WHERE  p.project_id = $project_id
    AND    p.project_id = pmp.project_id
    AND    pmp.account_id = a.account_id
	    ") or die(mysql_error());

  // sort the languages by
  $languages_query = mysql_query("
    SELECT  pl.language_id, l.language_name
    FROM    tr_project_languages pl, tr_languages l
    WHERE   project_id = $project_id AND
            pl.language_id = l.language_id
    ORDER BY l.language_name
	    ") or die(mysql_error());

	$infohash = array();
	while ($field = mysql_fetch_assoc($project_query))
    $infohash = $field;

  $infohash["project_managers"] = array();
	while ($field = mysql_fetch_assoc($project_manager_query))
    $infohash["project_managers"][] = $field;

  $infohash["languages"] = array();
	while ($field = mysql_fetch_assoc($languages_query))
    $infohash["languages"][] = $field;

	return $infohash;
}


/**
 * An array of language_ids ordered by language name.
 */
function ot_get_project_languages($project_id)
{
  $query = mysql_query("
    SELECT pl.language_id, l.language_name
		FROM   tr_project_languages pl, tr_languages l
		WHERE  pl.project_id = $project_id
		AND    pl.language_id = l.language_id
    ORDER BY l.language_name
	    ") or die(mysql_error());

	$language_info = array();
	while ($field = mysql_fetch_assoc($query))
    $language_info[] = $field;

  return $language_info;
}


/**
 * Retrieves all information about a project in a hash, project found via the version ID.
 * This just passes off all the heavy lifting to ot_get_project().
 *
 * @param integer $version_id
 */
function ot_get_project_from_version_id($version_id)
{
  $project_query = mysql_query("
    SELECT p.*
		FROM   tr_projects p, tr_project_versions pv
		WHERE  pv.version_id = $version_id AND
           p.project_id = pv.project_id
    LIMIT 1
	    ");
  $project_id = "";
	$infohash = array();
	while ($field = mysql_fetch_assoc($project_query))
  {
    $infohash = $field;
    $project_id = $field["project_id"];
  }

  return ot_get_project($project_id);
}


/**
 * Gets the project ID from a version ID.
 */
function ot_get_project_id_from_version_id($version_id)
{
  $project_query = mysql_query("
    SELECT p.project_id
		FROM   tr_projects p, tr_project_versions pv
		WHERE  pv.version_id = $version_id AND
           p.project_id = pv.project_id
    LIMIT 1
	    ");

  $info = mysql_fetch_assoc($project_query);
  $project_id = $info["project_id"];

  return $project_id;
}


/**
 * Retrieves all project info
 */
function ot_get_projects($status = "")
{
  $where_clause = "";
  if (!empty($status))
    $where_clause = "WHERE status = '$status'";

  $query = mysql_query("
    SELECT *
		FROM   tr_projects
    $where_clause
		ORDER BY name
	    ");

	$infohash = array();
	while ($field = mysql_fetch_assoc($query))
    $infohash[] = $field;

	return $infohash;
}


/**
 * Deletes an entire project, data, translators - the whole shebang.
 */
function delete_project($project_id)
{
  mysql_query("
    DELETE FROM tr_projects
		WHERE  project_id = $project_id
	    ");

  mysql_query("
    DELETE FROM tr_project_translators
		WHERE  project_id = $project_id
	    ");

  // get a list of all project versions
  $version_query = mysql_query("
    SELECT *
    FROM tr_project_versions
		WHERE  project_id = $project_id
	    ");

  // now delete everything to do with the specific versions. Data is associated with a particular
  // version, so the delete_project_version function will delete the project data and translations.
  while ($version = mysql_fetch_assoc($version_query))
  {
    $version_id = $version["version_id"];
    ot_delete_project_version($version_id);
  }
}


/**
 * Updates the main settings for a project
 */
function ot_update_project_settings($project_id)
{
  global $g_root_url, $LANG;

  $form_vals = ot_clean_hash($_POST);
  $status             = $form_vals["status"];
  $project_visibility = $form_vals["project_visibility"];
  $trust_threshold    = $form_vals["trust_threshold"];
  $translator_blacklist_threshold = $form_vals["translator_blacklist_threshold"];
  $enable_ftp = $form_vals["enable_ftp"];

  // these fields are only sent along if enable FTP is enabled
	$ftp_hostname    = isset($form_vals["ftp_hostname"]) ? $form_vals["ftp_hostname"] : "";
	$ftp_site_folder = isset($form_vals["ftp_site_folder"]) ? $form_vals["ftp_site_folder"] : "";
	$ftp_username    = isset($form_vals["ftp_username"]) ? $form_vals["ftp_username"] : "";
	$ftp_password    = isset($form_vals["ftp_password"]) ? $form_vals["ftp_password"] : "";

  $now = ot_get_current_datetime();

  // get the previous project settings
  $old_project_settings = ot_get_project($project_id);
  $old_trust_threshold = $old_project_settings["trust_threshold"];
  $old_translator_blacklist_threshold = $old_project_settings["translator_blacklist_threshold"];

  // step 1: update the new settings
  $query = mysql_query("
    UPDATE tr_projects
    SET    last_modified = '$now',
           status = '$status',
           project_visibility = '$project_visibility',
           trust_threshold = '$trust_threshold',
           translator_blacklist_threshold = '$translator_blacklist_threshold',
           enable_ftp = '$enable_ftp',
           ftp_hostname = '$ftp_hostname',
           ftp_site_folder = '$ftp_site_folder',
           ftp_username = '$ftp_username',
           ftp_password = '$ftp_password'
    WHERE  project_id = $project_id;
      ");

  // if the trust threshold is set to 0 ("Not Applicable"), set the "percent reliable" values for
  // all project-version-languages to 100%
  if ($trust_threshold == 0)
  {
    $versions = ot_get_project_versions($project_id);

    foreach ($versions as $version_info)
    {
      $version_id = $version_info["version_id"];
      mysql_query("UPDATE tr_project_version_language_stats SET percent_reliability = 100 WHERE version_id = $version_id");
    }
  }


  // TODO: if the trust threshold has changed - up or down - update all data in this project
  // for ALL translations.


  // TODO: when blacklist functionality is added, need to update blacklist if blacklist threshold
  // changes here

  return array(true, $LANG["text_project_settings_updated"]);
}


/**
 * Updates the project name and project description. Called by administrators / project managers.
 */
function ot_update_project_description($project_id)
{
  global $g_root_url, $LANG;

  $project_name        = $_POST["project_name"];
  $project_description = $_POST["project_description"];

  $now = ot_get_current_datetime();

  // connect to db and extract info about this user's account
  $query = mysql_query("
    UPDATE tr_projects
    SET    last_modified = '$now',
           name = '$project_name',
           description = '$project_description'
    WHERE  project_id = $project_id
      ");

  return array(true, "The project description has been updated.");
}


/**
 * Updates the project's translator notes. Called by administrators / project managers.
 */
function ot_update_project_translator_notes($project_id)
{
  global $g_root_url, $LANG;

  $translator_notes = $_POST["translator_notes"];

  $now = ot_get_current_datetime();

  // connect to db and extract info about this user's account
  $query = mysql_query("
    UPDATE tr_projects
    SET    last_modified = '$now',
           translator_notes = '$translator_notes'
    WHERE  project_id = $project_id
      ");

  return array(true, "The translator notes have been updated.");
}


/**
 * Called on the main language page. This updates the list of languages that are relevant to a project.
 *
 * @param integer $project_id
 * @param array $info
 * @return array
 */
function ot_update_project_languages($project_id, $info)
{
	// extract the languages
  $version_languages = $info["selected_languages"];

  $old_languages_query = mysql_query("SELECT language_id FROM tr_project_languages WHERE project_id = $project_id");
	$old_languages = array();
	while ($row = mysql_fetch_assoc($old_languages_query))
	  $old_languages[] = $row["language_id"];

	// find out if any have been deleted
	$langs_to_be_deleted = array_diff($old_languages, $version_languages);
	$langs_to_be_deleted_with_translations = array();
  $version_ids = ot_get_project_version_ids($project_id);

  if (!empty($langs_to_be_deleted))
  {
	  $version_id_str = join(", ", $version_ids);

  	// find out which of these languages that have been slated for deletion already have translations. For the
  	// moment, to keep things simple we're ONLY going to delete those languages that don't have any data. We'll inform
  	// the user of which languages
    foreach ($langs_to_be_deleted as $language_id)
    {
      $query = mysql_query("
         SELECT count(*) as c
         FROM   tr_data_translations dt, tr_data d
         WHERE  dt.language_id = $language_id AND
                d.data_id = dt.data_id AND
                d.version_id IN ($version_id_str)
           ");
      $result = mysql_fetch_assoc($query);
      $num_results = $result["c"];
      if ($num_results > 0)
      {
        $langs_to_be_deleted_with_translations[] = $language_id;
      }
      else
      {
        // here, there's no translations made in any of the versions for this language. Cool! Delete it!
        mysql_query("DELETE FROM tr_project_languages WHERE project_id = $project_id and language_id = $language_id");

        foreach ($version_ids as $version_id)
        {
          mysql_query("DELETE FROM tr_project_version_language_stats WHERE version_id = $version_id AND language_id = $language_id");
        }
      }
    }
  }

	// now insert any new version languages. At this point, we can assume that any languages that are no longer required have
	// been completely deleted
  foreach ($info["selected_languages"] as $language_id)
  {
    // if this record already exists, ignore it. Otherwise add it
    $query  = mysql_query("SELECT count(*) as c FROM tr_project_languages WHERE project_id = $project_id AND language_id = $language_id");
    $result = mysql_fetch_row($query);
    $already_exists = (isset($result["c"]) && $result["c"] == 1) ? true : false;
		$now = ot_get_current_datetime();

    if (!$already_exists)
    {
    	// add the new record to the tr_project_languages table
			mysql_query("
				INSERT INTO tr_project_languages (project_id, language_id)
				VALUES ($project_id, $language_id)
						");

      foreach ($version_ids as $version_id)
      {
				// finally, add the version-language statistics records
				mysql_query("
				  INSERT INTO tr_project_version_language_stats (version_id, language_id)
				  VALUES ($version_id, $language_id)
				  ");
      }
	  }
  }

  // could be improved!
  if (!empty($langs_to_be_deleted_with_translations))
  {
    return array(true, "The project languages have been updated, however one or more languages already have translations made so they could not be deleted [can be improved].");
  }
  else
    return array(true, "The project languages have been updated.");
}