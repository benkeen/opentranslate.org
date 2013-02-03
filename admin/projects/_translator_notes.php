<?php

require("../../global/library.php");
ot_check_permission("project_manager");

$project_id = $_SESSION["ot"]["project_id"];

if (isset($_POST["update"]))
  list($success, $message) = ot_update_project_translator_notes($project_id);


// get all info required for this project
$project  = ot_get_project($project_id);
$versions = ot_get_project_versions($project_id);

$page = "notes_for_translators";
