<?php

require("../global/library.php");
$request = array_merge($_POST, $_GET);
ot_check_permission("project_manager");

// add in the account type
$request["account_type"] = $_SESSION["ot"]["account_type"];

if (isset($request["update_account"]))
  list($success, $message) = ot_update_account($_SESSION["ot"]["account_id"], $request);

$account_info = ot_get_account($_SESSION["ot"]["account_id"]);
$languages    = ot_get_languages("ui");

$page = "my_account";