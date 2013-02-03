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

if (isset($request["update"]))
{
	list($success, $message) = ot_update_project_languages($project_id, $request);
}

// get all info required for this project
$project = ot_get_project($project_id);
$languages = ot_get_languages();

$page = "languages";