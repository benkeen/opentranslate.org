<?php

require("../global/library.php");
ot_check_permission("translator");

$translator_id = $_SESSION["ot"]["account_id"];

$projects = ot_get_translator_projects($translator_id);
$translator = ot_get_translator($translator_id);
$translator_project_languages = ot_get_translator_project_languages($translator_id);


// get a list of ALL projects that this translator can see - i.e. any that require translations
// between two languages that they speak. This includes those projects to which they're already
// assigned
$available_projects = ot_get_all_available_projects_by_language($translator["language_ids"]);

$page = "dashboard";
