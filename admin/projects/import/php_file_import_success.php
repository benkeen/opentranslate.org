<?php
session_start();
header("Cache-control: private");
require("_php_file_import_review.php");
?>
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
                  array($LANG["label_import_data"], "index.php"),
                  array($LANG["label_data_imported"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["label_data_imported"]?></h1>

  <p><?=$LANG["text_php_import_success_page_summary"]?></p>

  <?=ot_display_message($success, $message)?>

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
