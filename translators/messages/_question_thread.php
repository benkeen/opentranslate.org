<?php

require("../../global/library.php");
ot_check_permission("translator");

$request = array_merge($_POST, $_GET);
$translator_id = $_SESSION["ot"]["account_id"];
$project_id = $_SESSION["ot"]["project_id"];
$question_id = $request["question_id"];

if (isset($request["add_comment"]))
  list($success, $message) = ot_add_response($translator_id, $question_id, $request);

$question_thread = ot_get_translator_project_question_thread($question_id);
