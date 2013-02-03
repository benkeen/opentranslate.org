<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');

require("_feedback.php");
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
  $g_page_title = $LANG["word_administration"];
  require("$g_root_dir/global/header_code.php");
  ?>
	<script type="text/javascript" src="/global/general.js"></script>
</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/translators/"),
                  array("Feedback / Contact Us", "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1>Feedback / Contact Us</h1>
  <br />
  <?=ot_display_message($success, $message)?>

  <div>
    Have a suggestion? Found a bug? Use the form below to contact us.
  </div>
  <br />

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">

    <table cellspacing="1" cellpadding="1" border="0">
    <tr>
      <td width="90">Subject</td>
      <td width="540"><input type="text" name="subject" style="width:100%" /></td>
    </tr>
    <tr>
      <td valign="top" colspan="2">Comments</td>
    </tr>
    <tr>
      <td colspan="2">
        <div><textarea name="comments" rows="15" style="width: 100%"></textarea></div>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <div style="margin-top:5px;"><input type="submit" name="send" value=" SEND " class="blue bold" /></div>
      </td>
    </tr>
    </table>


  </form>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>

