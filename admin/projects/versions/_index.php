<?php

require("../../../global/library.php");
ot_check_permission("project_manager");

// if required, delete a project version
if (isset($_GET['delete']) && !empty($_GET['delete']))
  ot_delete_project_version($_GET['delete']);


// get all info required for this project
$project   = ot_get_project($_SESSION["ot"]["project_id"]);
$versions  = ot_get_project_versions($_SESSION["ot"]["project_id"]);

$page = "versions";