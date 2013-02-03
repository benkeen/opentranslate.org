<?php

require("../../../global/library.php");
ot_check_permission("admin");
$project_id = $_SESSION["ot"]["project_id"];
$version_id = $_SESSION["ot"]["version_id"];
$data_id        = $request["data_id"];
$translation_id = $request["translation_id"];

if (isset($request["update"]))
{
  list($success, $message) = ot_admin_update_translation($translation_id, $request["translation"]);
}

$project = ot_get_project($project_id);
$version = ot_get_project_version($version_id);
$data    = ot_get_data($data_id);

$translation          = ot_get_data_translation_by_translation_id($translation_id);
$target_language_name = ot_get_language_name($translation["language_id"]);
$origin_language_name = ot_get_language_name($project["origin_language_id"]);

$tiny_mce_mode = ($data["use_html_editor"] == "yes") ? "exact" : "none";
