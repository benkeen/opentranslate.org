<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');
require("_project_summary_file.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = "";
  require("$g_root_dir/global/header_code.php");
  ?>
</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_projects"], "$g_root_url/admin/projects/"),
                  array($project["name"], "../project.php"),
                  array($LANG["label_export_data"], "index.php"),
									array("View Project Summary File", "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <?php require("../../../global/change_project_version_form.php"); ?>

  <h1 class="margin_bottom_large">View Project Summary File</h1>

  <?=ot_display_message($success, $message)?>

  <textarea style="width:100%; height:500px"><?=$file_str?></textarea>

  <p>
    <a href="index.php">&laquo; Back to Export</a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
