<?php

require("../../../global/library.php");
ot_check_permission("project_manager");

$project_id = $_SESSION["ot"]["project_id"];

if (isset($_POST["add_version"]))
{
  ot_add_project_version($project_id);

  header("location: index.php");
  exit;
}

// get all info required for this project
$project   = ot_get_project($project_id);
$versions  = ot_get_project_versions($project_id);