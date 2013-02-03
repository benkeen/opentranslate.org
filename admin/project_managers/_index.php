<?php

require("../../global/library.php");

if (isset($_GET["delete"]) && !empty($_GET["delete"]))
  ot_delete_project_manager($_GET["delete"]);

$project_managers = ot_get_project_managers();

$page = "project_managers";
