<?php

require("../../../global/library.php");
ot_check_permission("project_manager");

$project_id = $_SESSION["ot"]["project_id"];
$project    = ot_get_project($project_id);
$origin_language_id = $project["origin_language_id"];

$current_project_translators_page = ot_load_field("proj_translators", "project_{$project_id}_translators_page", 1);
$translator_results = ot_get_project_translators($project_id, $current_project_translators_page);
$translator_info    = $translator_results["results"];
$num_translators    = $translator_results["num_results"];


$translator_ids = array();
foreach ($translator_info as $row)
  $translator_ids[] = $row["translator_id"];

$page = "translators";
