<?php

require("../../../global/library.php");
ot_check_permission("project_manager");

$account_id = $_SESSION["ot"]["account_id"];
$project_id = $_SESSION["ot"]["project_id"];
$data_id    = $request["data_id"];
$translator_id = $request["translator_id"];

if (isset($request["add_comment"]))
{
  $question_id = $request["question_id"];
  list($success, $message) = ot_add_data_response($account_id, $question_id, $request);
}

$question_thread = ot_get_translator_project_data_question_thread($data_id, $translator_id);
$project         = ot_get_project($project_id);
