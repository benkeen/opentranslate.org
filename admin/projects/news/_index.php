<?php

require("../../../global/library.php");
ot_check_permission("project_manager");

if (!isset($_SESSION["ot"]["project_id"]))
{
  header("location: ../project.php");
  exit;
}

$project_id = $_SESSION["ot"]["project_id"];

if (isset($request["delete"]))
  ot_delete_news_item($request["delete"]);

// get all info required for this project
$project = ot_get_project($project_id);
$news    = ot_get_news($project_id);

$page = "news";
