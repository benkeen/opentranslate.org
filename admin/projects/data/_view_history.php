<?php

require("../../../global/library.php");
ot_check_permission("admin");
$success = "";
$message = "";

$version_id = $_SESSION["ot"]["version_id"];
$project_id = $_SESSION["ot"]["project_id"];
$translator_id = $_SESSION["ot"]["account_id"];
$data_id        = $request["data_id"];
$translation_id = $request["translation_id"];

$project = ot_get_project($project_id);
$trust_threshold = $project["trust_threshold"];

// if required, perform the approval override for this translation
if (isset($request['approve_most_recent_translation']))
  list($success, $message) = ot_set_data_translation_approval_override($translation_id, $translator_id, $trust_threshold);

// if required, delete the entire translation history for this item [this deletes the translation, history and reviews]
if (isset($request['set_translation_as_current']))
{

}

// if required, delete most recent translation
if (isset($request['delete_translation']))
{
  ot_delete_translation_history_entry($request["translation_history_id"]);
}


// get all info required for this project
$data                = ot_get_data($data_id);
$translation         = ot_get_data_translation_by_translation_id($translation_id);
$translation_history = ot_get_data_translation_history($translation_id);
$translation_reviews = ot_get_data_translation_reviews($translation_id);
