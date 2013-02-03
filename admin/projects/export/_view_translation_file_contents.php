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
$language_name = ot_get_language_name($language_id);

$versions = ot_get_project_versions($project_id);

$file_str = ot_generate_php_language_file($version_id, $language_id);

$project = ot_get_project($project_id);

