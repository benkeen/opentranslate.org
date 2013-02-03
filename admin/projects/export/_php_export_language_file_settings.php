<?php

require("../../../global/library.php");
ot_check_permission("admin");
$success = "";
$message = "";

$project_id = $_SESSION["ot"]["project_id"];
$version_id = $_SESSION["ot"]["version_id"];
$language_id = $request["language_id"];
$project  = ot_get_project($project_id);
$versions = ot_get_project_versions($project_id);

// if required, update the PHP export settings
if (isset($request["update_settings"]))
  list($success, $message) = ot_update_version_language_php_export_settings($version_id, $language_id, $request["php_filename"]);

$language_name   = ot_get_language_name($language_id);
$export_settings = ot_get_version_language_info($version_id, $language_id);

