<?php

require("../../global/library.php");
ot_check_permission("translator");

$request = array_merge($_POST, $_GET);

// if there isn't a project ID in sessions, boot them out to the dashboard
if (!isset($_SESSION["ot"]["project_id"]) || empty($_SESSION["ot"]["project_id"]))
{
  header("location: $g_root_url/translators");
  exit;
}

$translator_id = $_SESSION["ot"]["account_id"];
$project_id    = $_SESSION["ot"]["project_id"];

// if there's a version ID and a language ID specified in $request, the translator has just selected
// something to translate. Store them in sessions then redirect.
if (isset($request["version_id"]) && !empty($request["version_id"]) &&
    isset($request["language_id"]) && !empty($request["language_id"]))
{
  $_SESSION["ot"]["version_id"] = $request["version_id"];
  $_SESSION["ot"]["project_{$project_id}_language_id"] = $request["language_id"];

  header("location: index.php");
  exit;
}

$project  = ot_get_project($project_id);
$versions = ot_get_project_versions($project_id);
$project_languages = ot_get_project_languages($project_id);

$translator_info = ot_get_translator($translator_id);
