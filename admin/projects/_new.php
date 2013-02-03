<?php

require("../../global/library.php");
ot_check_permission("admin");

$project_info = array();

if (isset($_POST['add_project']) && !empty($_POST['add_project']))
{
  $project_id = ot_add_project();

  header("location: project.php?project_id=$project_id");
  exit;
}

$languages = ot_get_languages();
$project_managers = ot_get_project_managers();