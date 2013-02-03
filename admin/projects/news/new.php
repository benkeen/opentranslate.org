<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');
require("_new.php");
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

  function toggle_email_translators_option(checked)
  {
    if (checked)
    {
      $("send_summary_email").disabled = false;
      $("send_summary_label").style.color = "#333333";
    }
    else
    {
      $("send_summary_email").disabled = true;
      $("send_summary_label").style.color = "#cccccc";
    }
  }
  </script>

  <style type="text/css">
  #send_summary_label { color: #cccccc; }
  </style>
  <?php
  $g_page_title = "";
  require("$g_root_dir/global/header_code.php");
  ?>
</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_projects"], "../"),
                  array($_SESSION["ot"]["project_name"], "../project.php"),
                  array("Add News", "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1>Add News</h1>

  <p>
    This page lets you add a new news item which will be displayed by all translators assigned to
    your project. If you wish to email the news to your translators as well, click the checkbox
    below. An additional option is provided to send yourself a summary email, containing a list of
    all translators that have been notified.
  </p>

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">

		<table cellspacing="0" cellpadding="1" class="info" width="100%">
		<tr>
			<td width="120">Subject</td>
			<td><input type="text" name="subject" style="width: 100%" /></td>
		</tr>
		<tr>
			<td valign="top">Message</td>
			<td><textarea name="message" style="width: 100%; height: 60px;"></textarea></td>
		</tr>
		<tr>
			<td> </td>
			<td class="no_underline">
			  <input type="checkbox" name="email_translators" id="email_translators" onchange="toggle_email_translators_option(this.checked)" />
			  <label for="email_translators">Email this news item to translators</label>
			</td>
		</tr>
		<tr>
			<td> </td>
			<td class="no_underline">
			  <input type="checkbox" name="send_summary_email" id="send_summary_email" disabled />
			  <label for="send_summary_email" id="send_summary_label">Send me a summary email</label>
			</td>
		</tr>
		</table>

		<p>
      <input type="submit" name="add_news" value="Add News" />
		</p>

  </form>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
