<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');
require("_version_export_settings.php");
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
									array("Version Export Settings", "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <?php require("../../../global/change_project_version_form.php"); ?>

  <h1>Version Export Settings</h1>
	<br />

  <?=ot_display_message($success, $message)?>

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">

    <table class="info" width="100%" cellpadding="1" cellspacing="0">
    <tr>
      <td width="20" valign="top" class="red">*</td>
      <td width="200" valign="top">PHP Translation Variable Name</td>
      <td><b>$</b><input type="text" name="php_translation_var_name" value="<?=$export_settings['php_translation_var_name']?>" size="10" /></td>
    </tr>
    <tr>
      <td width="20" valign="top" class="red"></td>
      <td width="200" valign="top">File Header Comments (PHP)</td>
      <td><textarea name="php_comments_header" style="width:100%; height: 100px;"><?=$export_settings['php_comments_header']?></textarea></td>
    </tr>
    </table>

    <p>
      <input type="submit" name="update_settings" value="<?=$LANG["word_update"]?>" />
    </p>

  </form>

  <div class="hr"></div>

  <p>
    <a href="index.php">&laquo; Back to Export</a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
