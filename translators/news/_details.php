<?php

require("../../global/library.php");
ot_check_permission("translator");

$request = array_merge($_POST, $_GET);
$translator_id = $_SESSION["ot"]["account_id"];
$project_id = $_SESSION["ot"]["project_id"];
$news_id = $request["news_id"];

// mark this news as having been read by this translator
ot_mark_news_as_read($translator_id, $news_id);

$news = ot_get_news_item($news_id);
