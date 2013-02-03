<?php

require("../../../global/library.php");
ot_check_permission("project_manager");
$project_id = $_SESSION["ot"]["project_id"];
$news_id = $request["news_id"];

// add new category
if (isset($_POST['update']))
{
  ot_update_news_item($news_id, $_POST);

  header("location: index.php");
  exit;
}

$news = ot_get_news_item($news_id);
$read_list = ot_get_news_item_read_by_translator_list($news_id);
$project_translator_info = ot_get_project_translators($project_id, 1, true);
$project_translators = $project_translator_info["results"];
