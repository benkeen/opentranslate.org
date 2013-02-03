<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');
require("_news_item.php");
?>
<html dir="<?=$LANG['direction']?>">
<head>
  <script language="javascript" type="text/javascript" src="/global/tiny_mce/tiny_mce.js"></script>
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
    content_css : "/global/tinymce.css"
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
                  array($_SESSION["ot"]["project_name"], "../project.php"),
                  array("Messages", "index.php"),
                  array("News Item", "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1>News Item</h1>

  <br />

  <table cellspacing="0" cellpadding="3" class="info" width="100%">
  <tr>
  	<td width="120" class="blue">Subject</td>
  	<td class="bold"><?=$news["subject"]?></td>
  </tr>
  <tr>
  	<td valign="top" class="blue no_underline">Message</td>
  	<td class="no_underline"><?=$news["message"]?></td>
  </tr>
  </table>

  <div class="hr"></div>

  <p>
    <a href="index.php">&lt;&lt; Back to Messages</a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
