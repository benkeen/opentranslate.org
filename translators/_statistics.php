<?php

require("../global/library.php");
ot_check_permission("translator");

$request = array_merge($_POST, $_GET);

// if in query string, set the version ID in sessions
if (isset($request["version_id"]) && !empty($request["version_id"]))
  $_SESSION["ot"]["version_id"] = $request["version_id"];

$project_id = $_SESSION["ot"]["project_id"];
$version_id = $_SESSION["ot"]["version_id"];
$project  = ot_get_project($project_id);
$versions = ot_get_project_versions($project_id);

// if there's no version ID specified in sessions here, grab the first version ID from $versions
if (!isset($_SESSION["ot"]["version_id"]) || empty($_SESSION["ot"]["version_id"]))
{
  $_SESSION["ot"]["version_id"] = $versions[0]["version_id"];
  $version_id = $_SESSION["ot"]["version_id"];
}

$statistics_query = ot_get_project_version_statistics($version_id);

$page = "statistics";