<?php

require("../../global/library.php");
ot_check_permission("translator");

$request  = array_merge($_POST, $_GET);

$project_id = $_SESSION["ot"]["project_id"];
$version_id = $_SESSION["ot"]["version_id"];
$translator_id = $_SESSION["ot"]["account_id"];
$data_id = $request["data_id"];
$target_language_id = $_SESSION["ot"]["project_{$project_id}_language_id"];


if (isset($request["edit_translation"]) || isset($request["edit_translation"]))
{
  $translation_id = $request["translation_id"];
  $translation = $request["translation"];
  ot_update_translation($data_id, $translation_id, $translator_id, $target_language_id, $translation);

  header("location: index.php");
  exit;
}


$project  = ot_get_project($project_id);
$version  = ot_get_project_version($version_id);
$data     = ot_get_data_translation($data_id, $target_language_id);

// if this user is set as the last reviewer, then allow them to edit the item
$allow_editing = false;
if ($translator_id == $data["last_reviewer_id"])
{
  ot_lock_translation_language($translator_id, $data_id, $target_language_id);
  $allow_editing = true;
}


$target_language_name = ot_get_language_name($target_language_id);
$origin_language_name = ot_get_language_name($project["origin_language_id"]);
