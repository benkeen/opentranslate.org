<?php

require("../../global/library.php");
ot_check_permission("admin");

$success = false;
$message = "";

$page = array();
$page["first_name"] = "";
$page["last_name"] = "";
$page["email"] = "";
$page["password"] = "";
$page["ui_language_id"] = "";
$page["receive_email_notifications"] = "";

if (isset($_POST) && !empty($_POST))
{
  list($success, $message) = ot_add_project_manager($_POST);

  if ($success)
  {
    header("location: index.php");
    exit;
  }

  $page["first_name"] = $_POST["first_name"];
  $page["last_name"] = $_POST["last_name"];
  $page["email"] = $_POST["email"];
  $page["password"] = $_POST["password"];
  $page["ui_language_id"] = $_POST["ui_language_id"];
  $page["receive_email_notifications"] = $_POST["receive_email_notifications"];
}

$languages = ot_get_languages("ui");