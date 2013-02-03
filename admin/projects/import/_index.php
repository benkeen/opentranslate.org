<?php

require("../../../global/library.php");
ot_check_permission("project_manager");

$project_id = $_SESSION["ot"]["project_id"];
$version_id = $_SESSION["ot"]["version_id"];

if (isset($_POST["import_data"]))
{
  list($success, $message) = ot_get_php_import_file_vars($project_id, $version_id, $_FILES["php_file"]);

  if ($success)
  {
    // store the variables in sessions
    $_SESSION["ot"]["php_import_vars"] = $message;
    header("location: php_file_import_review.php");
    exit;
  }
}

$project = ot_get_project($project_id);

$page = "import";