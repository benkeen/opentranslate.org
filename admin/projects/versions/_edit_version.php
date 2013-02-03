<?php

require("../../../global/library.php");
ot_check_permission("project_manager");

$version_id = $request["version_id"];

if (isset($_POST["update_version"]))
{
  ot_update_project_version($version_id);

  header("location: index.php");
  exit;
}


// get all info required for this project
$project = ot_get_project($_SESSION["ot"]["project_id"]);
$versions = ot_get_project_versions($_SESSION["ot"]["project_id"]);
ot_check_project_version($request, $versions);
$version = ot_get_project_version($version_id);
$languages = ot_get_languages();
$origin_language_id = $project["origin_language_id"];
$export_types = split(",", $version["export_types"]);
