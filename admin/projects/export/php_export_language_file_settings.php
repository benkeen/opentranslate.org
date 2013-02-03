<?php
session_start();
header("Cache-control: private");
header("Content-Type: text/html; charset=utf-8");
require("_php_export_language_file_settings.php");
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
                  array("Language File Settings", "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1>PHP Export File: <?=$language_name?></h1>
  <br />

  <?=ot_display_message($success, $message)?>

  <div>This page lets you control the specific PHP filename for this language.</div>
  <br />

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
    <input type="hidden" name="language_id" value="<?=$language_id?>" />

    <table class="info" width="100%" cellpadding="1" cellspacing="0">
    <tr>
      <td width="20" valign="top" class="red">*</td>
      <td width="200" valign="top">PHP Language File Filename</td>
      <td><input type="text" name="php_filename" value="<?=$export_settings['php_filename']?>" size="30" /></td>
    </tr>
    </table>

    <p>
      <input type="submit" name="update_settings" value="<?=$LANG["word_update"]?>" />
    </p>

  </form>

  <p>
    <a href="index.php">&laquo; Back to PHP Export</a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
