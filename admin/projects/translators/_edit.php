 <?php

require("../../../global/library.php");
ot_check_permission("project_manager");

$project_id = ot_load_field("project_id", "project_id");
$translator_id = $request["translator_id"];
$curr_tab = 1;

if (isset($_POST['update_translator']))
{
  list($success, $message) = ot_update_translator("project_manager", $translator_id, $_POST);
  $curr_tab = 1;
}
else if (isset($_POST["update_statistics"]))
{
  list($success, $message) = ot_update_total_translators_stats($translator_id);
  $curr_tab = 2;
}
else if (isset($_POST["update_translator_projects"]))
{
  ot_update_translator_projects($translator_id, $_POST);
  $curr_tab = 3;
}

$languages  = ot_get_languages();
$project    = ot_get_project($project_id);

// always store the project name in sessions [hack, for when we linked directly to this page] 
$_SESSION["ot"]["project_name"] = $project["name"];

$translator = ot_get_translator($translator_id);
$translator_stats = ot_get_all_translator_points($translator_id);

// retrieves all project that are RELEVANT for a translator (includes ones they're not assigned to)
$translator_projects = ot_get_translator_projects($translator_id);

// retrieves all project languages that the translator has signed up for
$translator_projects_languages = ot_get_translator_project_languages($translator_id);
