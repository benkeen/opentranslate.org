<?php

require("../global/library.php");
ot_check_permission("project_manager");

$projects = ot_get_projects();
$project_ids = array();
foreach ($projects as $project)
  $project_ids = $project["project_id"];

$num_days = 7;
$activity = ot_get_project_activity($project_ids, $num_days);

$page = "dashboard";
