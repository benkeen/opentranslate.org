<?php

require("../../../global/library.php");
ot_check_permission("project_manager");


if (isset($request["change_project_version"]) && is_numeric($request["version_id"]))
{
	$_SESSION["ot"]["version_id"] = $request["version_id"];
}
$project_id = $_SESSION["ot"]["project_id"];
$version_id = ot_load_field("version_id", "version_id");
 
$project  = ot_get_project($project_id);
$versions = ot_get_project_versions($project_id);

$origin_language_id = $project["origin_language_id"];
$origin_language    = ot_get_language_name($origin_language_id);

// if there's no version ID specified in sessions here, grab the first version ID from $versions
if (!isset($_SESSION["ot"]["version_id"]) || empty($_SESSION["ot"]["version_id"]))
{
  $_SESSION["ot"]["version_id"] = $versions[0]["version_id"];
  $version_id = $_SESSION["ot"]["version_id"];
}

$file_str = ot_generate_php_project_version_summary_file($version_id);

$page = "export";
