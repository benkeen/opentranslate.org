<?php

require("../../global/library.php");
ot_check_permission("translator");

$success = "";
$message = "";

$request = array_merge($_POST, $_GET);
$project_id = $_SESSION["ot"]["project_id"];
$version_id = $_SESSION["ot"]["version_id"];
$translator_id = $_SESSION["ot"]["account_id"];
$target_language_id = $_SESSION["ot"]["project_{$project_id}_language_id"];

if (isset($request["translate"]))
{
  $trust_threshold = $_SESSION["ot"]["project"]["trust_threshold"];
  $origin_language_id = $_SESSION["ot"]["project"]["origin_language_id"];

  list($success, $message) = ot_make_bulk_review($origin_language_id, $target_language_id, $translator_id, $trust_threshold, $_POST);

  // if there are no errors, redirect back to the main page.
  if (!in_array(false, $success))
  {
    header("location: index.php");
    exit;
  }
}

$project  = ot_get_project($project_id);
$version  = ot_get_project_version($version_id);
$search_data = ot_get_multiple_data_translations($_SESSION["ot"]["project"]["bulk_review_data_ids"], $target_language_id);

$target_language_name = ot_get_language_name($target_language_id);
$origin_language_name = ot_get_language_name($project["origin_language_id"]);

// get a list of all the fields that require the HTML editor.
$html_fields = array();
while ($row = mysql_fetch_assoc($search_data))
{
  if ($row["use_html_editor"] == "yes")
    $html_fields[] = "translation_{$row['data_id']}";
}
$html_fields_str = join(",", $html_fields);
mysql_data_seek($search_data, 0);
