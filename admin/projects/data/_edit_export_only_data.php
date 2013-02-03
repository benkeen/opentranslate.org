<?php

require("../../../global/library.php");
ot_check_permission("admin");
$version_id = $_SESSION["ot"]["version_id"];
$project_id = $_SESSION["ot"]["project_id"];
$data_id = $request["data_id"];

if (isset($request['delete']))
{
  ot_delete_data($request['delete']);
  header("location: index.php");
  exit;
}

if (isset($_POST["update_data"]))
{
  // update the data
  ot_update_data($data_id, $_POST);

  // update / insert the special data for export in the various language files
  ot_update_export_only_data($data_id, $_POST);

  header("location: index.php");
  exit;
}

// get all info required for this project
$project       = ot_get_project($project_id);
$categories    = ot_get_categories($project_id);
$data          = ot_get_data($data_id);
$translations  = ot_get_data_translations($data_id);
$tiny_mce_mode = ($data["use_html_editor"] == "yes") ? "exact" : "none";
$origin_language_id = $project["origin_language_id"];

$category_is_export_only = false;
foreach ($categories as $category_info)
{
  if ($category_info["category_id"] == $data["category_id"])
  {
    $category_is_export_only = ($category_info["export_only"] == "yes") ? true : false;
    break;
  }
}
