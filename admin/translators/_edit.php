<?php

require("../../global/library.php");
$success = "";
$message = "";
$request = array_merge($_POST, $_GET);
$translator_id = $request["translator_id"];
$curr_tab = 1;

if (isset($_POST['update_translator']))
{
  list($success, $message) = ot_update_translator("admin", $translator_id, $_POST);
  $curr_tab = 1;
}
else if (isset($_POST["update_statistics"]))
{
  list($success, $message) = ot_update_total_translators_stats($translator_id);
  $curr_tab = 2;
}
else if (isset($_POST["update_translator_projects"]))
{
  ot_update_translator_projects($translator_id);
  $curr_tab = 3;
}

$languages  = ot_get_languages();
$translator = ot_get_translator($translator_id);
$translator_stats = ot_get_all_translator_points($translator_id);

// retrieves all projects that this translator is currently associated with
$translator_projects = ot_get_translator_projects($translator_id);
$translator_projects_languages = ot_get_translator_project_languages($translator_id);
