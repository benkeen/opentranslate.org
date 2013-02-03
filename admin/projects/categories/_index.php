<?php

require("../../../global/library.php");
ot_check_permission("project_manager");

if (isset($request["change_project_version"]) && is_numeric($request["version_id"]))
{
	$_SESSION["ot"]["version_id"] = $request["version_id"];
}

$project_id = $_SESSION["ot"]["project_id"];
$version_id = $_SESSION["ot"]["version_id"];

$versions = ot_get_project_versions($project_id);
ot_check_project_version($request, $versions);

// add new category
if (isset($_POST['add']) && !empty($_POST['new_category']))
  ot_add_category($version_id, $_POST['new_category']);

// reorder categories
else if (isset($_POST['update']) && !empty($_POST['update']))
{
  ot_reorder_categories();
  ot_update_categories();
}

// delete category
else if (isset($_GET['delete']) && !empty($_GET['delete']))
  ot_delete_category($version_id, $_GET['delete']);


$categories = ot_get_categories($project_id);
$project    = ot_get_project($project_id);

// if there are no categories for this version, check to see if it's a
$base_version_id = ot_get_base_version($version_id);
$is_child_version = ($version_id != $base_version_id) ? true : false;

$page = "categories";
