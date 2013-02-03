<?php

require("../../../global/library.php");
ot_check_permission("project_manager");
$project_id = $_SESSION["ot"]["project_id"];

// add new category
if (isset($_POST['add_news']))
{
  ot_add_news_item($project_id, $_POST);

  header("location: index.php");
  exit;
}