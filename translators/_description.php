<?php

require("../global/library.php");
ot_check_permission("translator");

$request = array_merge($_POST, $_GET);
$project_id = $request["project_id"];
$translator_id = $_SESSION["ot"]["account_id"];

// get all info required for this project
$project    = ot_get_project($project_id);
$translator = ot_get_translator($translator_id);
$origin_language_id = $project["origin_language_id"];
$origin_language_name = ot_get_language_name($origin_language_id);
$translator_project_languages = ot_get_translator_project_languages($translator_id);

// sign up this translator to the project!
if (isset($request["join"]))
{
  ot_add_translator_to_project($translator_id, $project_id, $request["selected_translations"]);

  header("location: index.php");
  exit;
}
