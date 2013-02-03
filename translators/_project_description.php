<?php

require("../global/library.php");
ot_check_permission("translator");

$request = array_merge($_POST, $_GET);

$project_id = $_SESSION["ot"]["project_id"];
$project = ot_get_project($project_id);

$page = "project_description";
