<?php

require("../global/library.php");
$success = "";
$message = "";
ot_check_permission("translator");

$project_id = $_SESSION["ot"]["project_id"];


// get all info required for this project
$project  = ot_get_project($project_id);
$versions = ot_get_project_versions($project_id);

$page = "notes_for_translators";
