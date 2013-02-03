<?php

require("../../global/library.php");
ot_check_permission("translator");
$request = array_merge($_POST, $_GET);

if (!isset($_SESSION["ot"]["project_id"]))
{
  header("location: ../project.php");
  exit;
}

$project_id = $_SESSION["ot"]["project_id"];
$translator_id = $_SESSION["ot"]["account_id"];

// get all info required for this project
$project = ot_get_project($project_id);
$news    = ot_get_news($project_id);

$unread_news_ids = ot_get_unread_news_ids($translator_id, $project_id);
$num_unread_news = count($unread_news_ids);

$page = "news";