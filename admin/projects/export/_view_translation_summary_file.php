<?php

require("../../../global/library.php");
ot_check_permission("project_manager");
$project_id = $_SESSION["ot"]["project_id"];
$version_id = $_SESSION["ot"]["version_id"];

$project = ot_get_project($project_id);

if (isset($request["view"]))
  $file_str = ot_generate_php_project_version_summary_file($version_id);
