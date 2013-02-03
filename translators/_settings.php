<?php

require("../global/library.php");
ot_check_permission("translator");

$request = array_merge($_POST, $_GET);
$project_id = $_SESSION["ot"]["project_id"];
$translator_id = $_SESSION["ot"]["account_id"];

if (isset($_POST["leave_project"]))
{
  ot_remove_translator_from_project($project_id, $translator_id);
  header("location: index.php");
  exit;
}

if (isset($request["update_settings"]))
{
  list($success, $message) = ot_update_translator_project_settings($translator_id, $project_id, $request);
}

$project   = ot_get_project($project_id);
$languages = ot_get_languages();
$translator = ot_get_translator($translator_id);
$translator_project_settings = ot_get_translator_project_settings($translator_id, $project_id);
$origin_language_id = $project["origin_language_id"];
$origin_language_name = ot_get_language_name($origin_language_id);
$translator_project_languages = ot_get_translator_project_languages($translator_id);

$page = "settings";
