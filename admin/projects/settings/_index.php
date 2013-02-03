<?php

require("../../../global/library.php");
ot_check_permission("project_manager");
$success = "";
$message = "";

$project_id = $_SESSION["ot"]["project_id"];

if (isset($_POST["update_settings"]) || isset($_POST["test_ftp_settings"]))
{
  list($success, $message) = ot_update_project_settings($project_id);

  if ($success && isset($_POST["test_ftp_settings"]))
  {
    list($success, $message) = ot_test_ftp_settings($request);

    // mark the FTP settings as confirmed or not
    ot_set_project_ftp_settings_confirmed($project_id, $success);
  }
}

$project   = ot_get_project($project_id);
$languages = ot_get_languages();
$origin_language_name = ot_get_language_name($project["origin_language_id"]);

$page = "settings";