<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');
require("_question_thread.php");
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
                  array($LANG["word_projects"], "$g_root_url/admin/projects/"),
                  array($project["name"], "../project.php"),
                  array($LANG["word_data"], "./index.php"),
                  array($LANG["label_manage_data"], "./edit_data.php?data_id=$data_id"),
                  array("Questions", ""),
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1>Question</h1>

  <br />

  <table cellpadding="1" cellspacing="0">
  <tr>
  	<td width="120">Subject</td>
  	<td class="italic">
      <?=$question_thread[0]["subject"]?>
    </td>
  </tr>
  </table>

  <br />

  <hr size="1" />

  <table class="info" width="100%" cellpadding="1" cellspacing="0">
  <?php
  $accounts = array();
  $question_id = $question_thread[0]["question_id"];
  $language_id = $question_thread[0]["language_id"];
  for ($i=0; $i<count($question_thread); $i++)
  {
    $status = $question_thread[$i]["status"];
 	  $creation_date = ot_get_date("", $question_thread[$i]["creation_date"], "M jS Y, g:i A");
    $message = $question_thread[$i]["message"];
    $question_account_id = $question_thread[$i]["account_id"];

    // if we haven't already asked the database for information on this account, do so now
    if (!array_key_exists($question_account_id, $accounts))
      $accounts[$question_account_id] = ot_get_account($question_account_id);

    // piece together what we want to display
    $account_type = "";
    if ($accounts[$question_account_id]["account_type"] != "translator")
    {
      $account_type = preg_replace("/_/", " ", $accounts[$question_account_id]["account_type"]);
      $account_type = ", " . ucwords($account_type);
    }
    $display_name = "{$accounts[$question_account_id]["first_name"]} {$accounts[$question_account_id]["last_name"]}$account_type";

    // if the row is unread and NOT written by the translator, highlight it
    $css_class = "";
    if ($status == "unread" && $account_id != $question_account_id)
      $css_class = "highlight";

    echo "
      <tr class=\"$css_class\">
        <td>

          <table width=\"100%\" cellpadding=\"1\" cellspacing=\"0\" style=\"margin-top: 6px; margin-bottom: 6px;\">
          <tr>
            <td width=\"120\" class=\"bold no_underline\">Author</td>
            <td class=\"no_underline\">$display_name</td>
          </tr>
          <tr>
            <td class=\"bold no_underline\">Written</td>
            <td class=\"no_underline\">$creation_date</td>
          </tr>
          <tr>
            <td class=\"bold no_underline\">Message</td>
            <td class=\"no_underline\">$message</td>
          </tr>
          </table>

        </td>
      </tr>";
  }
  ?>
  </table>

  <br />

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
    <input type="hidden" name="data_id" value="<?=$data_id?>" />
    <input type="hidden" name="question_id" value="<?=$question_id?>" />
    <input type="hidden" name="translator_id" value="<?=$translator_id?>" />
    <input type="hidden" name="project_id" value="<?=$project_id?>" />
    <input type="hidden" name="language_id" value="<?=$language_id?>" />

    <div id="comment_box">
      <textarea name="message" style="width:100%; height: 80px;"></textarea>
    </div>

    <p>
      <input type="submit" name="add_comment" value="Add Comment &raquo;" class="blue bold" />
    </p>

  </form>

  <div class="hr"></div>

  <p>
    <a href="edit_data.php?data_id=<?php echo $data_id;?>&tab=3">&lt;&lt; Back to Manage Data</a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>

<?php

// now mark any "unread" responses as read
ot_mark_data_responses_as_read($account_id, $question_id);

?>