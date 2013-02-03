<?php

require("../../../global/library.php");
ot_check_permission("project_manager");

if (isset($request["change_project_version"]) && is_numeric($request["version_id"]))
{
	$_SESSION["ot"]["version_id"] = $request["version_id"];
}
$project_id = $_SESSION["ot"]["project_id"];
$version_id = $_SESSION["ot"]["version_id"];
$account_id = $_SESSION["ot"]["account_id"];
$curr_tab = 1;

// delete category
if (isset($request['delete']) && !empty($request['delete']))
  ot_delete_data($request['delete']);
else if (isset($request['update_settings']) && !empty($request['update_settings']))
{
  ot_update_account_settings_translations_page($account_id, $version_id, $_POST);
  $curr_tab = 2;
}

// -------------------------------------------------------------------------

// get all info required for this project
$project    = ot_get_project($project_id);
$versions   = ot_get_project_versions($project_id);
$categories = ot_get_categories($project_id);
$project_languages = ot_get_project_languages($project_id);

$language_id = ot_load_field("language_id", "language_id", $project_languages[0]["language_id"]);

ot_check_project_version($request, $versions);

if (isset($_GET["reset"]) && $_GET["reset"] == "1")
{
  unset($_SESSION["ot"]["version_{$version_id}_data_sort_order"]);
  unset($_SESSION["ot"]["version_{$version_id}_data_select_all_ids"]);
}


// which page are we viewing?
$current_page = ot_load_field("page", "version_{$version_id}_data_page");
if (empty($current_page) || isset($_POST["search"]))
{
	$current_page = 1;
	$_SESSION["ot"]["version_{$version_id}_data_page"] = 1;
}

// determine the order
if (isset($_GET["order"]))
{
  $_SESSION["ot"]["version_{$version_id}_data_sort_order"] = $_GET["order"];
  $order = $_GET["order"];
}
else
{
  if (isset($_SESSION["ot"]["version_{$version_id}_data_sort_order"]))
    $order = $_SESSION["ot"]["version_{$version_id}_data_sort_order"];
  else
    $order = "data_category_order-ASC";
}

// limited to a particular category?
$g_category_id = ot_load_field("category_id", "version_{$version_id}_category_id");
$g_category_id = ot_load_field("category_id", "version_{$version_id}_category_id");
$g_data_size   = ot_load_field("data_size", "version_{$version_id}_data_size");

// performing a search?
$g_search_criteria = array();
if (isset($request["search"]))
{
  $g_search_criteria["data_string"] = isset($request["data_string"]) ? $request["data_string"] : "";
}

$results_per_page = $_SESSION["ot"]["ui_num_data_per_page"];
$results_info = ot_search_data($version_id, $results_per_page, $current_page, $order, $language_id, $g_category_id,
  $g_data_size, $g_search_criteria);

$search_query = $results_info["search_query"];     // extract the MySQL query resource
$num_results  = $results_info["num_results"];      // extract the total number of results found
$num_rows_in_page = mysql_num_rows($search_query); // the results on this CURRENT page


// check that the current page is stored in sessions is, in fact, a valid option. e.g.
// if the person was having 10 submissions listed per page, had 11 submissions, and was on
// page 2 before deleting the 11th, when they returned to this page, they'd have page 2 stored
// in sessions, although there is no longer a second page.
$total_pages = ceil($num_results / $results_per_page);
if (isset($_SESSION["version_{$version_id}_data_page"]) && $_SESSION["version_{$version_id}_data_page"] > $total_pages)
  $_SESSION["version_{$version_id}_data_page"] = $total_pages;

$preselected_ids = isset($_POST['data']) ? $_POST['data'] : "";
if (empty($preselected_ids))
  $preselected_ids = array();

// see if any settings
$settings_data_columns = array();
if (isset($account_settings["version_{$version_id}_data_columns"]))
  $settings_data_columns = split(",", $account_settings["version_{$version_id}_data_columns"]);

$page = "translations";
