<?php

require("../../../global/library.php");
ot_check_permission("admin");
$success = "";
$message = "";

$project_id = $_SESSION["ot"]["project_id"];
$version_id = $_SESSION["ot"]["version_id"];

if (isset($_POST["import_data"]))
{
  $num_rows = $_POST["num_rows"];

  for ($i=1; $i<=$num_rows; $i++)
  {
    $info["data"] = $_POST["row_{$i}_value"];
    $info["data_label"] = $_POST["row_{$i}_label"];
    $info["category_id"] = $_POST["row_{$i}_category_id"];
    $info["insert_position"] = "end";

    $info["comments_for_translators"] = "";
    $info["use_html_editor"] = "no";
		$info["version_id"] = $version_id;

    ot_add_data($version_id, $info);
  }

  header("location: php_file_import_success.php?num_data=$num_rows");
  exit;
}

$project = ot_get_project($project_id);
$php_vars = $_SESSION["ot"]["php_import_vars"];
$category_info = ot_get_categories($project_id);
