<?php

require("../global/library.php");
ot_check_permission("translator");

$success = "";
$message = "";

$request = array_merge($_POST, $_GET);

// this user is a translator, so explicitly set it for the update_account function
$request["account_type"] = "translator";

if (isset($request["update_account"]))
  list($success, $message) = ot_update_account($_SESSION["ot"]["account_id"], $request);

$account_info = ot_get_translator($_SESSION["ot"]["account_id"]);
$languages    = ot_get_languages();
$ui_languages = ot_get_languages("ui");

$page = "my_account";
