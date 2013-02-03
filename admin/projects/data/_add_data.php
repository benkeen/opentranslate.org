<?php

require("../../../global/library.php");
ot_check_permission("project_manager");

if (isset($request["change_project_version"]) && is_numeric($request["version_id"]))
{
	$_SESSION["ot"]["version_id"] = $request["version_id"];
}
$version_id = $_SESSION["ot"]["version_id"];
$project_id = $_SESSION["ot"]["project_id"];

if (isset($_POST["add_data"]))
{
  ot_add_data($version_id, $_POST);

  header("location: index.php");
  exit;
}

// get all info required for this project
$project      = ot_get_project($_SESSION["ot"]["project_id"]);
$versions     = ot_get_project_versions($project_id);
$categories   = ot_get_categories($project_id);
$version_data = ot_get_version_data($version_id);

$category_data = array();
foreach ($version_data as $data_info)
{
  if (!array_key_exists($data_info["category_id"], $category_data))
    $category_data[$data_info["category_id"]] = array();

  $category_data[$data_info["category_id"]][$data_info["data_id"]] = $data_info["data_label"];
}