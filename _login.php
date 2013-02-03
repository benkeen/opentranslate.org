<?php

// empty all sessions
require("global/library.php");

$page = array();
$page["email"]    = "";
$page["password"] = "";

if (isset($_GET["redirect"]))
{
  $_SESSION["ot"]["login_redirect"]        = $_GET["redirect"];
  $_SESSION["ot"]["login_redirect_values"] = $_GET["redirect_values"];
}
if (isset($_POST["log_in"]) && !empty($_POST["log_in"]))
{
  list($success, $message) = ot_login($_POST);

	$page["email"] = $_POST["email"];
	$page["password"] = $_POST["password"];
}
