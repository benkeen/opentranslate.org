<?php

/*------------------------------------------------------------------------------------------------*\
  Copyright (C) 2010 Benjamin Keen
  http://www.opentranslate.org
\*------------------------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------------------------*\
  VERSIONS - functions in brief

	  ot_add_project_version
		ot_get_project_version
		ot_get_project_versions
    ot_delete_project_version
    ot_update_project_version
    ot_get_version_languages
    ot_check_project_version
    ot_check_version_unique

\*------------------------------------------------------------------------------------------------*/


/**
 * Adds a new version to the database.
 */
function ot_add_project_version($project_id)
{
	$version_label = $_POST["version_label"];
	$synopsis      = $_POST["synopsis"];
	$is_visible    = $_POST["is_visible"];
	$may_translate = $_POST["may_translate"];
  $use_data_source = isset($_POST["data_source"]) ? true : false;

  $is_base_version   = "yes";
  $parent_version_id = "NULL";
  if ($use_data_source)
  {
    $is_base_version = "no";
    $parent_version_id = $_POST["parent_version_id"];
  }

	$now   = ot_get_current_datetime();
	$query = mysql_query("
	  INSERT INTO tr_project_versions (project_id, is_base_version, parent_version_id, version_label, synopsis,
	    date_created, last_modified, may_translate, is_visible)
		VALUES ($project_id, '$is_base_version', $parent_version_id, '$version_label', '$synopsis', '$now', '$now',
		  '$may_translate', '$is_visible')
      ") or die(mysql_error());
  $new_version_id = mysql_insert_id();

  // add the version language statistics rows. These are used to track the state of the translations for
  // each project-version-language
  $query = mysql_query("SELECT * FROM tr_project_version_language_stats WHERE version_id = $parent_version_id");
  while ($row = mysql_fetch_assoc($query))
  {
    $language_id = $row["language_id"];
    $percent_translated  = $row["percent_translated"];
    $percent_reliability = $row["percent_reliability"];
    $php_filename = $row["php_filename"];
    $php_export_status = $row["php_export_status"];
    $translation_last_change_date = $row["translation_last_change_date"];

    mysql_query("
      INSERT INTO tr_project_version_language_stats (version_id, language_id, percent_translated,
        percent_reliability, php_filename, php_export_status, translation_last_change_date)
      VALUES  ($new_version_id, $language_id, $percent_translated,
        $percent_reliability, '$php_filename', '$php_export_status', '$translation_last_change_date')
        ") or die(mysql_error());
  }

  return $new_version_id;
}


/**
 * This function finds the base version of a project version. The idea is that we can have chains of
 * versions - each inheriting the content from another, and only overriding whatever data is required.
 * This keeps duplication in the database to an absolute minimum. This function returns the single,
 * final base version ID for a version ID which may be anywhere in the chain.
 *
 * Returns false if the database version info is invalid (shouldn't ever happen!)
 *
 * @param integer $version_id
 * @return mixed
 */
function ot_get_base_version($version_id)
{
  // assume the current version is the base version ID
  $current_version_id = $version_id;

  while (true)
  {
    $query = mysql_query("
    	SELECT is_base_version, parent_version_id
    	FROM   tr_project_versions
    	WHERE  version_id = $current_version_id
    	  ");

    // this should never happen, but just in case...
    if (mysql_num_rows($query) == 0)
      break;

    $result = mysql_fetch_assoc($query);
    $is_base_version = $result["is_base_version"];

    if ($is_base_version != "yes")
    {
    	if (empty($result["parent_version_id"]) | !is_numeric($result["parent_version_id"]))
    	{
    	  $current_version_id = false;
    	  break;
    	}
    	$current_version_id = $result["parent_version_id"];
    }
    else
    {
    	break;
    }
  }

  return $current_version_id;
}


/**
 * This is similar to ot_get_base_version except that it returns an array of parent versions. The order
 * they are returned in the chain from the base version (index 0) and includes the version being passed. If
 * the $version_id passed has no parents, it returns an array with itself as the only member.
 *
 * @param integer $version_id
 * @return array [Base Version, Child 1, Child 2, $version_id (passed as param)]
 */
function ot_get_parent_versions($version_id)
{
  // assume the current version is the base version ID
  $current_version_id = $version_id;
  $chain_version_ids  = array();

  while (true)
  {
  	$chain_version_ids[] = $current_version_id;
    $query = mysql_query("
      SELECT is_base_version, parent_version_id
      FROM   tr_project_versions
      WHERE  version_id = $current_version_id
        ");

    // this should never happen, but just in case...
    if (mysql_num_rows($query) == 0)
      break;

    $result = mysql_fetch_assoc($query);
    $is_base_version = $result["is_base_version"];

    if ($is_base_version != "yes")
    {
    	if (empty($result["parent_version_id"]) | !is_numeric($result["parent_version_id"]))
    	{
    	  $current_version_id = false;
    	  break;
    	}
    	$current_version_id = $result["parent_version_id"];
    }
    else
    {
    	$current_version_ids[] = $result["parent_version_id"];
    	break;
    }
  }

  return array_reverse($chain_version_ids);
}


/**
 * Just like ot_get_parent_versions, except it returns the results in a tree format, with the final
 * key being the $version_id being passed (or an empty array, if the $version_id is a base version).
 *
 * @param integer $version_id
 * @return array
 */
function ot_get_parent_version_tree($version_id)
{
	$parent_version_ids = ot_get_parent_versions($version_id);

	if (count($parent_version_ids) <= 1)
	  return array();

	$reversed = array_reverse($parent_version_ids);
	$tree = array();
	foreach ($reversed as $version_id)
	{
		$tmp = array();
		$tmp[$version_id] = $tree;
		$tree = $tmp;
	}
  return $tree;
}


/**
 * Returns an array of child versions, EXCLUDING the version ID passed as a parameter. Since
 * a parent may have any number of children, this function returns a multi-dimensional hash.
 * Each key is a version ID, their values is a hash.
 *
 * @param integer $version_id
 * @return array
 */
function ot_get_child_version_tree($version_id)
{
  $child_versions = array();

  $query = mysql_query("
    SELECT version_id
    FROM   tr_project_versions
    WHERE  parent_version_id = $version_id
      ");

  while ($row = mysql_fetch_assoc($query))
  {
  	$version_id = $row["version_id"];
  	$child_versions[$version_id] = ot_get_child_version_tree($version_id);
  }

  return $child_versions;
}


function ot_get_child_versions($version_id)
{
  $tree = ot_get_child_version_tree($version_id);
  return ot_multiarray_keys($tree);
}


/**
 * This function returns a tree of all version IDs for a particular version.
 *
 * @param integer $version_id
 * @return array
 */
function ot_get_version_tree($version_id)
{
	$base_version_id = ot_get_base_version($version_id);
  $version_tree = array(
    $base_version_id => ot_get_child_version_tree($base_version_id)
      );
  return $version_tree;
}

function ot_get_project_version($version_id)
{
  $query = mysql_query("
    SELECT *
		FROM   tr_project_versions
		WHERE  version_id = $version_id
	    ");

  return mysql_fetch_assoc($query);
}


/**
 * Returns all versions for a project. Use ot_get_project_version_ids if you just
 * need the IDs.
 *
 * @param integer $project_id
 * @return array
 */
function ot_get_project_versions($project_id)
{
  $query = mysql_query("
    SELECT *
		FROM   tr_project_versions
		WHERE  project_id = $project_id
		ORDER BY date_created ASC
	    ");

	$infohash = array();
	while ($field = mysql_fetch_assoc($query))
    $infohash[] = $field;

  return $infohash;
}


/**
 * Returns an array of all project version IDs.
 *
 * @param integer $project_id
 * @return array
 */
function ot_get_project_version_ids($project_id)
{
  $query = mysql_query("
    SELECT version_id
		FROM   tr_project_versions
		WHERE  project_id = $project_id
		ORDER BY date_created ASC
	    ");

	$version_ids = array();
	while ($field = mysql_fetch_assoc($query))
    $version_ids[] = $field["version_id"];

  return $version_ids;
}


/**
 * Deletes a project version.
 */
function ot_delete_project_version($version_id)
{
	// get all the child versions for this version. We'll need that info to update the changes table
	// for each
  $child_version_ids = ot_get_child_versions($version_id);

  $version_info = ot_get_project_version($version_id);

  // TODO here, we need to get a list of all data IDs for this version and update the deletions & changes table
//  echo "incomplete. ot_delete_project_version()";
//  exit;

  // delete everything in the data table for this
	mysql_query("DELETE * FROM tr_data WHERE version_id = $version_id");

  // clean up the DB
	mysql_query("DELETE FROM tr_project_categories WHERE version_id = $version_id");
	mysql_query("DELETE FROM tr_project_version_language_stats WHERE version_id = $version_id");
	mysql_query("DELETE FROM tr_project_versions WHERE version_id = $version_id");
}


/**
 * Called by administrators and project managers only. Updates the main settings of a project version.
 */
function ot_update_project_version($version_id)
{
  $info = ot_clean_hash($_POST);

	$version_label = $info["version_label"];
	$export_folder = $info["export_folder"];
	$synopsis      = $info["synopsis"];
	$is_visible    = $info["is_visible"];
	$may_translate = $info["may_translate"];
  $version_languages = $info["selected_languages"];
	$export_types  = join(",", $info["export_types"]);
  $show_labels   = $info["show_labels_on_translator_pages"];

	$now = ot_get_current_datetime();

	$query = mysql_query("
	  UPDATE tr_project_versions
    SET    version_label = '$version_label',
		       export_folder = '$export_folder',
           synopsis = '$synopsis',
           may_translate = '$may_translate',
           is_visible = '$is_visible',
           export_types = '$export_types',
					 show_labels_on_translator_pages = '$show_labels'
    WHERE  version_id = $version_id
      ");
}


/**
 * Retrieve everything about a version-language; namely the specific export settings, state of
 * translation, etc. If the record doesn't exist, it creates a blank row and returns that.
 */
function ot_get_version_language_info($version_id, $language_id)
{
  $result = mysql_query("
    SELECT *
    FROM   tr_project_version_language_stats
    WHERE  version_id = $version_id AND
           language_id = $language_id
      ");

  $num_rows = mysql_num_rows($result);
  if ($num_rows == 0)
  {
    mysql_query("INSERT INTO tr_project_version_language_stats (version_id, language_id) VALUES ($version_id, $language_id)");

    $result = mysql_query("
      SELECT *
      FROM   tr_project_version_language_stats
      WHERE  version_id = $version_id AND
             language_id = $language_id
        ");
  }

  return mysql_fetch_assoc($result);
}


/**
 * Called on all pages which allow project managers to change the current version. It updates the current
 * project version in sessions ([ot][version_id])
 */
function ot_check_project_version($request, $versions)
{
  // if the user is changing the project version, store the new value in sessions
  if (isset($request["version_id"]))
  {
    $_SESSION["ot"]["version_id"] = $request["version_id"];

    foreach ($versions as $version)
    {
      if ($version["version_id"] == $request["version_id"])
        $_SESSION["ot"]["version_label"] = $version["version_label"];
    }
  }

  // otherwise, if there's no version ID stored in sessions
  else
  {
    if (!isset($_SESSION["ot"]["version_id"]))
    {
      if (isset($versions[0]["version_id"]))
      {
        $_SESSION["ot"]["version_id"] = $versions[0]["version_id"];
        $_SESSION["ot"]["version_label"] = $versions[0]["version_label"];
      }
    }
  }
}


/**
 * Helper function to return the name (label) of a version.
 *
 * @param integer $version_id
 * @return string
 */
function ot_get_version_name($version_id)
{
  $result = @mysql_query("SELECT version_label FROM tr_project_versions WHERE version_id = $version_id");

  $version_label = "";
  if ($result)
  {
  	$info = mysql_fetch_assoc($result);
  	$version_label = $info["version_label"];
  }

  return $version_label;
}

