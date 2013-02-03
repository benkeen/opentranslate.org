<?php

require("../../global/library.php");
ot_check_permission("translator");
$success = "";
$message = "";

$request  = array_merge($_POST, $_GET);

$project_id = $_SESSION["ot"]["project_id"];
$version_id = $_SESSION["ot"]["version_id"];
$translator_id = $_SESSION["ot"]["account_id"];
$data_id = (isset($request["data_id"])) ? $request["data_id"] : $_SESSION["ot"]["project"]["data_size"];
$target_language_id = $_SESSION["ot"]["project_{$project_id}_language_id"];

if (isset($request["review"]))
{
  $data_id         = $_SESSION["ot"]["project"]["data_id"];
  $data_size       = $_SESSION["ot"]["project"]["data_size"];
  $trust_threshold = $_SESSION["ot"]["project"]["trust_threshold"];
  $origin_language_id = $_SESSION["ot"]["project"]["origin_language_id"];

  list($success, $message) = ot_review_translation($data_id, $origin_language_id, $target_language_id, $translator_id, $trust_threshold, $data_size, $_POST);

  if ($success)
  {
    header("location: index.php");
    exit;
  }
}

$project  = ot_get_project($project_id);
$version  = ot_get_project_version($version_id);
$data     = ot_get_data_translation($data_id, $target_language_id);

$target_language_name = ot_get_language_name($target_language_id);
$origin_language_name = ot_get_language_name($project["origin_language_id"]);

// store some information about the data in sessions to reduce prevent hacking attempts
$_SESSION["ot"]["project"]["data_id"] = $data_id;
$_SESSION["ot"]["project"]["data_size"] = $data["data_size"];
