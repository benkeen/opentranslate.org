<?php

require("../../global/library.php");
ot_check_permission("translator");
$request = array_merge($_POST, $_GET);

ot_unlock_expired_sessions();

// if there isn't a project ID in sessions, boot them out all the way to the dashboard
if (!isset($_SESSION["ot"]["project_id"]) || empty($_SESSION["ot"]["project_id"]))
{
  header("location: $g_root_url/translators");
  exit;
}
$project_id = $_SESSION["ot"]["project_id"];

if (isset($request["change_project_version"]) && is_numeric($request["version_id"]))
{
	$_SESSION["ot"]["version_id"] = $request["version_id"];
}

// if the person hasn't selected a project version AND a language, redirect them to the "select" page
if ((!isset($_SESSION["ot"]["version_id"]) || empty($_SESSION["ot"]["version_id"])) ||
    (!isset($_SESSION["ot"]["project_{$project_id}_language_id"]) || empty($_SESSION["ot"]["project_{$project_id}_language_id"])) ||
		(isset($_GET["select"]) && $_GET["select"] == 1))
{
  // explicitly empty the sessions in case the translator wants to translate something else
  unset($_SESSION["ot"]["project_{$project_id}_language_id"]);
  unset($_SESSION["ot"]["version_id"]);

  header("location: $g_root_url/translators/data/select.php");
  exit;
}

$version_id = $_SESSION["ot"]["version_id"];
$account_id = $_SESSION["ot"]["account_id"];
$language_id = $_SESSION["ot"]["project_{$project_id}_language_id"];
$target_language_name = ot_get_language_name($language_id);

// get all info required for this project
$project  = ot_get_project($project_id);
$versions = ot_get_project_versions($project_id);
$categories = ot_get_categories($project_id, false);
ot_check_project_version($request, $versions);
$reviewed_translation_ids = ot_get_reviewed_project_data_translations($version_id, $account_id, $language_id);

// which page are we viewing?
$current_page = ot_load_field("page", "version_{$version_id}_data_page");
if (empty($current_page) || isset($_POST["search"]))
  $current_page = 1;

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

$g_type_filter = ot_load_field("type_filter", "version_{$version_id}_type_filter");
$g_category_id = ot_load_field("category_id", "version_{$version_id}_category_id");
$g_data_size   = ot_load_field("data_size", "version_{$version_id}_data_size");

// performing a search?
$g_search_criteria = array();
if (isset($request["search"]))
{
  $g_search_criteria["data_string"] = isset($request["data_string"]) ? $request["data_string"] : "";
}

$results_per_page = $_SESSION["ot"]["ui_num_data_per_page"];
$results_info = ot_search_data_translation($version_id, $results_per_page, $current_page, $order, $language_id,
	$account_id, $g_type_filter, $g_category_id, $g_data_size, $g_search_criteria);

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

// TODO. Huh. this line was defined earlier. CHeck it out
//$version_languages  = ot_get_version_languages($version_id);


// WRONG TODO
//$version_languages = ot_get_available_project_version_languages($account_id, $project_id);

$page = "translate_now";
