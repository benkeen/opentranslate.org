<?php

// pulls in the appropriate
$account_type = (isset($_SESSION["ot"]["account_type"]) && !empty($_SESSION["ot"]["account_type"])) ? $_SESSION["ot"]["account_type"] : "public";

switch ($account_type)
{
  case "public":
    require("public_nav.php");
    break;
  case "project_manager":
    require("project_manager_nav.php");
    break;
  case "translator":
    require("translator_nav.php");
    break;
  case "admin":
    require("admin_nav.php");
    break;
}
?>
