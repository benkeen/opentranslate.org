<?php

require("../../../global/library.php");
ot_check_permission("project_manager");

if (isset($request["change_project_version"]) && is_numeric($request["version_id"]))
	$_SESSION["ot"]["version_id"] = $request["version_id"];
if (isset($request["version_id"]))
  $_SESSION["ot"]["version_id"] = $request["version_id"];

$version_id = $_SESSION["ot"]["version_id"];
$project_id = $_SESSION["ot"]["project_id"];
$account_id = $_SESSION["ot"]["account_id"];
$data_id = $request["data_id"];

if (isset($request['delete']))
{
  ot_delete_data($request['delete'], $request);

  header("location: index.php");
  exit;
}

if (isset($_POST["update_data"]))
{
  list($success, $message) = ot_update_data($data_id, $request);
}

// get all info required for this project
$project        = ot_get_project($project_id);
$categories     = ot_get_categories($project_id);
$data           = ot_get_data($data_id);
$translations   = ot_get_data_translations($data_id);
$data_questions = ot_get_data_questions($data_id, $account_id);

$tiny_mce_mode = ($data["use_html_editor"] == "yes") ? "exact" : "none";
$curr_tab = 1;

$category_is_export_only = false;
foreach ($categories as $category_info)
{
  if ($category_info["category_id"] == $data["category_id"])
  {
    $category_is_export_only = ($category_info["export_only"] == "yes") ? true : false;
    break;
  }
}

if (isset($request["tab"]))
  $curr_tab = $request["tab"];

