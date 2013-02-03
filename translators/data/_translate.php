<?php

require("../../global/library.php");
ot_check_permission("translator");

$request = array_merge($_POST, $_GET);
$project_id    = $_SESSION["ot"]["project_id"];
$translator_id = $_SESSION["ot"]["account_id"];
$data_id       = $request["data_id"];

// these tests are added so that a translator can link directly to this translation from the messages
// page. The link contains the version ID and language ID, letting them bypass the "select translation"
// page (e.g. English -> French, English -> Swahili)
if (isset($request["version_id"]))
{
  $version_id = $request["version_id"];
  $_SESSION["ot"]["version_id"] = $request["version_id"];
}
else
  $version_id = $_SESSION["ot"]["version_id"];

if (isset($request["target_language_id"]))
{
  $_SESSION["ot"]["project_{$project_id}_language_id"] = $request["target_language_id"];
  $target_language_id = $request["target_language_id"];
  $_SESSION["ot"]["project_{$project_id}_language_id"] = $request["target_language_id"];
}
else
  $target_language_id = $_SESSION["ot"]["project_{$project_id}_language_id"];


$trans = ot_get_data_translation($data_id, $target_language_id);
$curr_tab = 1;

// translate the text (single)
if (isset($request["translate"]))
{
  list($success, $message) = ot_make_translation($data_id, $target_language_id, $translator_id, $_POST);

  // if successful, redirect them back to the index page. If there was an error, it will be shown in
  // the page.
  if ($success)
  {
    header("location: index.php");
    exit;
  }
}
else
{
  // lock the translation [no check for existing lock? should! Same goes for bulk translate, review & view]
  ot_lock_translation_language($translator_id, $data_id, $target_language_id);
}


// add the question / comment
if (isset($request["add_question"]))
{
  $request["version_id"] = $version_id;
  list($success, $message) = ot_add_data_question($translator_id, $request);
  $curr_tab = 2;
}
if (isset($request["add_comment"]))
{
  $request["version_id"] = $version_id;
  list($success, $message) = ot_add_data_response($translator_id, $request["question_id"], $request);
  $curr_tab = 2;
}

if (isset($request["tab"]))
  $curr_tab = $request["tab"];


$project  = ot_get_project($project_id);
$version  = ot_get_project_version($version_id);
$data     = ot_get_data($data_id);
$tiny_mce_mode = ($data["use_html_editor"] == "yes") ? "exact" : "none";

$data_question_thread = ot_get_translator_project_data_question_thread($data_id, $translator_id);

$target_language_name = ot_get_language_name($target_language_id);
$origin_language_name = ot_get_language_name($project["origin_language_id"]);

