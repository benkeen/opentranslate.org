<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');

require("_view_translation_summary_file.php");
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
                  array("Translation Summary File (PHP)", "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1>Translation Summary File (PHP)</h1>
  <br />

  <textarea style="width:100%; height:500px"><?=$file_str?></textarea>

  <p>
    <a href="index.php"><?=$LANG["label_backtoproject"]?></a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
