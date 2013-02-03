<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');

require("_index.php");
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
                  array($LANG["nav_dashboard"], "$g_root_url/admin/"),
                  array($LANG["nav_list_projects"], ""),
                  array($project["name"], "project.php"),
                  array($LANG["word_categories"], ""),
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["label_edit_category"]?></h1>

  <br />

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
    <input type="hidden" name="reorder_categories" value="1" />
  </form>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
