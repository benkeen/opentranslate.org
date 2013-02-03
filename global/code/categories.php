<?php

/*------------------------------------------------------------------------------------------------*\
  Copyright (C) 2010 Benjamin Keen
  http://www.opentranslate.org
\*------------------------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------------------------*\
  CATEGORIES - functions in brief

    ot_add_category
    ot_get_category
		ot_get_category_name
    ot_get_categories
    ot_update_categories
    ot_delete_category
    ot_reorder_categories

\*------------------------------------------------------------------------------------------------*/


/**
 * Adds a new category to a particular version.
 */
function ot_add_category($version_id, $category_name)
{
  $project_id = ot_get_project_id_from_version_id($version_id);
	
  // find out how many categories are already listed
  $query = mysql_query("
    SELECT count(*)
    FROM   tr_project_categories
    WHERE  project_id = $project_id
      ");

  $result = mysql_fetch_row($query);

  $num_project_categories = $result[0];

  $category_order = $num_project_categories + 1;
  $category_name = mysql_real_escape_string($_POST['new_category']);

  // find out how many categories are already listed
  $query = mysql_query("
    INSERT INTO tr_project_categories
    SET         project_id = $project_id,
                parent_category_id = 0,
                category_name = '$category_name',
                category_order = $category_order
      ");
}


/**
 * Gets all information about a category
 */
function ot_get_category($category_id)
{
  $query = mysql_query("
    SELECT *
    FROM   tr_project_categories
    WHERE  category_id = $category_id
       ");

  return mysql_fetch_assoc($query);
}


/**
 * Returns the category string name
 */
function ot_get_category_name($category_id)
{
  $query = mysql_query("
    SELECT category_name
    FROM   tr_project_categories
    WHERE  category_id = $category_id
       ");

	$result = mysql_fetch_assoc($query);

  return $result["category_name"];
}


/**
 * Gets all categories for a given project. Right now, categories are shared among all project
 * versions.
 *
 * @param integer $project_id
 * @param boolean $include_export_only (T/F): whether it should return categories marked as export only
 */
function ot_get_categories($project_id, $include_export_only = true)
{
	$export_only_clause = "";
	if (!$include_export_only)
	  $export_only_clause = "AND export_only = 'no' ";

  $query = mysql_query("
    SELECT *
    FROM   tr_project_categories
    WHERE  project_id = $project_id
		       $export_only_clause
    ORDER BY category_order
       ");

  $infohash = array();
  while ($category_info = mysql_fetch_assoc($query))
    $infohash[] = $category_info;

  return $infohash;
}


/**
 * Updates the categories for a project
 */
function ot_update_categories()
{
  // loop through $_POST and for each field_X_order values, update the $curr
  while (list($key, $val) = each($_POST))
  {
    // find the field id
    preg_match("/^category_(\d+)_order$/", $key, $match);

    if (!empty($match[1]))
    {
      $category_id   = $match[1];
      $category_name = $_POST["category_{$category_id}_name"];
      $export_only = isset($_POST["export_only_{$category_id}"]) ? "yes" : "no";

      mysql_query("
        UPDATE tr_project_categories
        SET    category_name = '$category_name',
               export_only = '$export_only'
        WHERE  category_id = $category_id
           ") or die(mysql_error());
    }
  }
}


/**
 * Deletes a category for a given project.
 *
 * TODO BUG this orphans any data stored in that category!
 */
function ot_delete_category($project_id, $category_id)
{
  mysql_query("
    DELETE FROM  tr_project_categories
    WHERE  category_id = $category_id
       ");

  // now re-sort the rest
	$query = mysql_query("
	  SELECT *
		FROM   tr_project_categories
    WHERE  version_id = $version_id
		ORDER BY category_order
		  ");

	$new_order = 1;
  while ($result = mysql_fetch_assoc($query))
	{
	  $current_cat_id = $result["category_id"];

    mysql_query("
		  UPDATE  tr_project_categories
			SET     category_order = $new_order
			WHERE   category_id = $current_cat_id
			  ");

		$new_order++;
	}
}


/**
 * Reorders categories for a given project.
 */
function ot_reorder_categories()
{
  $new_order = array();

  // loop through $_POST and for each field_X_order values, update the $curr
  while (list($key, $val) = each($_POST))
  {
    // find the field id
    preg_match("/^category_(\d+)_order$/", $key, $match);

    if (!empty($match[1]))
    {
      $cat_id = $match[1];

      // update the $account_order
      $new_order[$cat_id] = $val;
    }
  }
  asort($new_order);
  reset($_POST);

  // now re-sort the categories
  $i = 1;
  while (list($cat_id, $value) = each($new_order))
  {
    mysql_query("
      UPDATE tr_project_categories
      SET    category_order = $i
      WHERE  category_id = $cat_id
						    ");
      $i++;
  }
}
