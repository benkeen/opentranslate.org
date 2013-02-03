<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');

require("_project_description.php");
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
                  array($LANG["word_dashboard"], "$g_root_url/translators/"),
                  array($project["name"], "project.php"),
                  array($LANG["label_project_description"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$project['name']?></h1>
	<br />

  <div><?=$project['description']?></div>

  <div class="hr"></div>

  <p>
    <a href="project.php"><?=$LANG["label_backtoproject"]?></a>
  </p>


<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
