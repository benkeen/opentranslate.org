<?php

require("../global/library.php");
$success = "";
$message = "";
ot_check_permission("translator");

$translator_id = $_SESSION["ot"]["account_id"];

if (isset($_POST["send"]))
{
  mail("ben.keen@gmail.com", $_POST["subject"], $_POST["comments"]);

  $success = true;
  $message = "Thanks for the email. Your feedback is appreciated!";
}

$page = "feedback";
