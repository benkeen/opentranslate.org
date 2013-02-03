<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');

require("_translator_notes.php");
?>
<html dir="<?=$LANG['direction']?>">
<head>

  <script language="javascript" type="text/javascript" src="<?=$g_root_url?>/global/tiny_mce/tiny_mce.js"></script>
  <script language="javascript" type="text/javascript">
  tinyMCE.init({
    mode : "textareas",
    theme : "advanced",
    theme_advanced_buttons1 : "bold,italic,underline,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,outdent,indent,separator,forecolor,backcolor,separator,cut,copy,paste,separator,link,unlink,hr,fontsizeselect,separator,code",
    theme_advanced_buttons2 : "",
    theme_advanced_buttons3 : "",
    theme_advanced_toolbar_align : "left",
    theme_advanced_path_location : "bottom",
    theme_advanced_toolbar_location : "top",
    theme_advanced_resize_horizontal : false,
    theme_advanced_resizing : true,
    content_css : "<?=$g_root_url?>/global/tinymce.css"
  });
  </script>

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
                  array($project["name"], "project.php"),
                  array("Notes for Translators", "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1 style="padding-bottom: 5px;">Notes for Translators</h1>
  <br />

  <?=ot_display_message($success, $message); ?>

  <div>
    This page lets you provide translators with further instructions and information about your project
    and translation methodology. Use this field to impart any information you wish the translator to know.
    This information will only be shown to translators who've signed up to your project.
  </div>

  <br />

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">

    <div><textarea name="translator_notes" rows="15" style="width: 680px"><?=$project['translator_notes']?></textarea></div>

    <p>
      <input type="submit" name="update" value="<?=$LANG['word_update']?>" />
    </p>

  </form>

  <br />
  <div class="hr"></div>

  <p>
    <a href="project.php"><?=$LANG["label_backtoproject"]?></a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
