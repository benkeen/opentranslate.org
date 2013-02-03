<?php

require("../../global/library.php");
ot_check_permission("project_manager");

$request = array_merge($_POST, $_GET);

if (isset($request["delete"]))
  ot_delete_project($request["delete"]);

$projects = ot_get_projects();

// empty any old project / version that was stored
unset($_SESSION["ot"]["project_id"]);
unset($_SESSION["ot"]["version_id"]);

$page = "projects";