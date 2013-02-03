<?php

require("../../../global/library.php");
ot_check_permission("project_manager");

$page['email']    = "";
$page["password"] = "";
$page["status"]   = "";
$page["first_name"]   = "";
$page["last_name"]   = "";
$page["ui_language_id"]   = "";
$project_id = $_SESSION["ot"]["project_id"];

if (isset($_POST['add_translator']))
{
  list($success, $message) = ot_add_translator($_POST);

  if ($success)
  {
    header("location: index.php");
    exit;
  }

  $page["email"]    = $_POST["email"];
  $page["password"] = $_POST["password"];
  $page["status"]   = $_POST["status"];
}

$languages  = ot_get_languages();
$project    = ot_get_project($project_id);
