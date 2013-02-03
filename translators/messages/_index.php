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

// General Questions
$current_project_question_page = ot_load_field("pq_page", "message_{$project_id}_project_question_page", 1);
$project_question_results = ot_get_translator_project_questions($translator_id, $project_id, $current_project_question_page);
$project_questions     = $project_question_results["results"];
$num_project_questions = $project_question_results["num_results"];

// Translation Questions
$current_translation_question_page = ot_load_field("tq_page", "message_{$project_id}_translation_question_page", 1);
$data_question_results = ot_get_translator_project_data_questions($translator_id, $project_id, $current_translation_question_page);
$translation_questions = $data_question_results["results"];
$num_translation_questions = $data_question_results["num_results"];

$page = "messages";
