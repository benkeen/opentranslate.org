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
  if (isset($request["bulk_translate_view"]))
    $_SESSION["ot"]["bulk_translate_view"] = $request["bulk_translate_view"];

  list($success, $message) = ot_make_bulk_translation($target_language_id, $translator_id, $request);

  // if there are no errors, redirect back to the main page.
  if (!in_array(false, $success))
  {
    header("location: index.php");
    exit;
  }
}
else
{
  // lock the translations
  foreach ($_SESSION["ot"]["project"]["bulk_translate_data_ids"] as $data_id)
    ot_lock_translation_language($translator_id, $data_id, $target_language_id);
}

$project  = ot_get_project($project_id);
$version  = ot_get_project_version($version_id);
$search_data = ot_get_multiple_data($_SESSION["ot"]["project"]["bulk_translate_data_ids"]);

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

// if the default view type isn't in sessions, grab the default value from their user account
if (!isset($_SESSION["ot"]["bulk_translate_view"]))
{
  $account_info = ot_get_translator($_SESSION["ot"]["account_id"]);
  $_SESSION["ot"]["bulk_translate_view"] = $account_info["default_bulk_translate_view"];
}
$default_bulk_translate_view = $_SESSION["ot"]["bulk_translate_view"];
