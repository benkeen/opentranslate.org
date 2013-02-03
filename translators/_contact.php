<?php

require("../global/library.php");
ot_check_permission("translator");
$translator_id = $_SESSION["ot"]["account_id"];
$project_id = $_SESSION["ot"]["project_id"];
$project = ot_get_project($project_id);

if (isset($_POST["send"]))
  list($success, $message) = ot_add_question($translator_id, $_POST);

$page = "contact";