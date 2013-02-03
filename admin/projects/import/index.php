<?php
session_start();
header("Cache-control: private");
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
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_projects"], "$g_root_url/admin/projects/"),
                  array($project["name"], "../project.php"),
                  array($LANG["label_import_data"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["label_import_data"]?></h1>

  <p><?=$LANG["text_php_import_page_summary"]?></p>

  <?=ot_display_message($success, $message)?>

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post" enctype="multipart/form-data">

    <table cellspacing="1" cellpadding="1" class="info">
    <tr>
      <td class="pad_right"><?=$LANG["label_php_file"]?></td>
      <td><input type="file" name="php_file" /></td>
    </tr>
    </table>

    <p>
      <input type="submit" name="import_data" value="<?=$LANG['label_import_data_uc']?>" />
    </p>

  </form>

  <br />
  <div class="hr"></div>

  <p>
    <a href="../project.php"><?=$LANG["label_backtoproject"]?></a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
