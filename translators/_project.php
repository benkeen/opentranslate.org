<?php

require("../global/library.php");

ot_check_permission("translator");
$request = array_merge($_POST, $_GET);

if (isset($request["project_id"]))
  $_SESSION["ot"]["project_id"] = $request["project_id"];
else if (!isset($_SESSION["ot"]["project_id"]))
{
  header("location: index.php");
  exit;
}

$translator_id = $_SESSION["ot"]["account_id"];
$project_id = $_SESSION["ot"]["project_id"];

$project = ot_get_project($project_id);

// always store the project name in sessions
$_SESSION["ot"]["project_name"] = $project["name"];

$versions = ot_get_project_versions($project_id);
ot_check_project_version($request, $versions);

// store info about the project in sessions for quick retrieval in subsequent pages
$_SESSION["ot"]["project"] = $project;

$unread_news_ids = ot_get_unread_news_ids($translator_id, $project_id);
$num_unread_news = count($unread_news_ids);

$page = "project_dashboard";