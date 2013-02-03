<?php

require("../../../global/library.php");
ot_check_permission("project_manager");

if (!isset($_SESSION["ot"]["project_id"]))
{
  header("location: ../project.php");
  exit;
}

$project_id = $_SESSION["ot"]["project_id"];
$account_id = $_SESSION["ot"]["account_id"];

// get all info required for this project
$project = ot_get_project($project_id);

// General Questions
$current_project_question_page = ot_load_field("pq_page", "message_{$project_id}_project_version_page", 1);
$project_question_results = ot_get_project_questions($project_id, $account_id, $current_project_question_page);
$project_questions     = $project_question_results["results"];
$num_project_questions = $project_question_results["num_results"];

$translator_questions = ot_get_project_data_questions($project_id, $account_id);

$page = "messages";