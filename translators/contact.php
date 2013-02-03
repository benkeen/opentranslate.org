<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');

require("_contact.php");
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

  var rules = [];
  rules.push("required,subject,Please enter the subject line.");
  </script>

  <?php
  $g_page_title = $LANG["word_administration"];
  require("$g_root_dir/global/header_code.php");
  ?>
	<script type="text/javascript" src="<?=$g_root_url?>/global/general.js"></script>
</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/translators/"),
                  array($project["name"], "project.php?project_id=$project_id"),
                  array("Contact Us", "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1>Contact Us</h1>
  <br />
  <?=ot_display_message($success, $message)?>

  <div>
    Use the form below for any questions you may have concerning the project. If you have a question
    about a particular piece of text for translation, please use the custom form on the translation
    page.
  </div>
  <br />

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post" onsubmit="return validateFields(this, rules)" >
    <input type="hidden" name="project_id" value="<?=$project_id?>" />

    <table cellspacing="1" cellpadding="1" border="0" width="100%">
    <tr>
      <td width="90" class="medium_grey">Subject</td>
      <td width="580"><input type="text" name="subject" style="width:100%" maxlength="255" /></td>
    </tr>
    <tr>
      <td valign="top" colspan="2" class="medium_grey">Message</td>
    </tr>
    <tr>
      <td colspan="2">
        <div><textarea name="message" rows="15" style="width: 100%"></textarea></div>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <div style="margin-top:5px;"><input type="submit" name="send" value=" SEND " class="blue bold" /></div>
      </td>
    </tr>
    </table>

  </form>

  <div class="hr"></div>

  <p>
    <a href="project.php">&laquo; Back to Project</a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>

