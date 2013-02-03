<?php

require("../library.php");

// a really crumby, temporary file for managing Ajax requests

if (empty($request["action"]))
  echo "No action specified";

switch ($request["action"])
{
  // adds a question from the Bulk Translate page.
  case "data_question":
    $translator_id = $request["translator_id"];
    list($success, $message) = ot_add_data_question($translator_id, $request);
    echo $message;
    break;

  case "auto_translate":
    $version_id = $request["version_id"];
    $language_id = $request["language_id"];
    $num_items = $request["num_items"];
  	list($success, $error, $num_translated_items, $num_remaining_items) = ot_auto_translate($version_id, $language_id, $num_items);
  	echo "{ success: $success, error: '$error', num_translated_items: $num_translated_items, num_remaining_items: $num_remaining_items }";
  	break;
}
