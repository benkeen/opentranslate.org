<?php

require("../../global/library.php");

ot_check_permission("admin");

$request = array_merge($_POST, $_GET);
$account_id = $request["account_id"];

if (isset($_POST) && !empty($_POST))
{
  ot_update_project_manager($_POST);

  header("location: index.php");
  exit;
}

$page      = ot_get_project_manager($account_id);
$languages = ot_get_languages();
$projects  = ot_get_projects();

$selected_project_ids = array();
foreach ($page["projects"] as $project)
  $selected_project_ids[] = $project["project_id"];
