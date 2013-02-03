<?php

require("../../global/library.php");
ot_check_permission("project_manager");
$request = array_merge($_POST, $_GET);

if (isset($request["project_id"]))
  $_SESSION["ot"]["project_id"] = $request["project_id"];
else if (!isset($_SESSION["ot"]["project_id"]))
{
  header("location: index.php");
  exit;
}

// get all info required for this project
$project  = ot_get_project($_SESSION["ot"]["project_id"]);

// always store the project name in sessions
$_SESSION["ot"]["project_name"] = $project["name"];

$versions = ot_get_project_versions($_SESSION["ot"]["project_id"]);
ot_check_project_version($request, $versions);

$page = "project_dashboard";
