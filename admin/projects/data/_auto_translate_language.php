<?php

require("../../../global/library.php");
ot_check_permission("project_manager");

if (isset($request["change_project_version"]) && is_numeric($request["version_id"]))
{
	$_SESSION["ot"]["version_id"] = $request["version_id"];
}

$project_id = $_SESSION["ot"]["project_id"];
$version_id = $_SESSION["ot"]["version_id"];
$language_id = ot_load_field("language_id", "language_id");

// if required, update the statistics for this project
if (isset($request["refresh_stats"]))
  ot_update_project_statistics($project_id);

$versions = ot_get_project_versions($project_id);
$project  = ot_get_project($project_id);

$statistics_query = ot_get_project_version_statistics($version_id);

$page = "auto_translate_language";
