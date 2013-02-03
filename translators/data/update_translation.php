<?php
session_start();
header("Cache-control: private");
header("Content-Type: text/html; charset=utf-8");

require("_update_translation.php");
?>
<html dir="<?=$LANG['direction']?>">
<head>
  <script type="text/javascript" src="<?=$g_root_url?>/global/tiny_mce/tiny_mce.js"></script>
  <script type="text/javascript">
    tinyMCE.init({
      mode : "<?=$tiny_mce_mode?>",
      elements : "translation",
      theme : "advanced",
      theme_advanced_toolbar_location : "top",
      theme_advanced_buttons1 : "bold,italic,underline,separator,justifyleft,justifycenter,justifyright,separator,bullist,numlist,hr,separator,code",
      theme_advanced_buttons2 : "",
      theme_advanced_buttons3 : "",
      theme_advanced_toolbar_align : "left",
      theme_advanced_path_location : "bottom",
      theme_advanced_resize_horizontal : false,
      theme_advanced_resizing : true,
      <?php
      if ($data["data_size"] > $g_PARAGRAPH_SIZE)
        echo 'height: "250",';
      else if ($data["data_size"] > $g_PHRASE_SIZE)
        echo 'height: "200",';
      else if ($data["data_size"] > $g_SENTENCE_SIZE)
        echo 'height: "100",';
      else
        echo 'height: "80",';
      ?>
      content_css : "<?=$g_root_url?>/global/tinymce.css"
    });

    tinyMCE.init({
      mode : "exact",
      elements : "message",
      theme : "advanced",
      theme_advanced_toolbar_location : "top",
      theme_advanced_buttons1 : "bold,italic,underline,separator,justifyleft,justifycenter,justifyright,separator,bullist,numlist,hr,separator,code",
      theme_advanced_buttons2 : "",
      theme_advanced_buttons3 : "",
      theme_advanced_toolbar_align : "left",
      theme_advanced_path_location : "bottom",
      theme_advanced_resize_horizontal : false,
      theme_advanced_resizing : true,
      height: "60",
      content_css : "<?=$g_root_url?>/global/tinymce.css"
    });

    var current_tab = <?=$curr_tab?>;

    function set_subject_to_other()
    {
      $("q2").checked = true;
    }
  </script>

	<script type="text/javascript" src="<?=$g_root_url?>/global/manage_lists.js"></script>
  <script type="text/javascript" src="<?=$g_root_url?>/global/tooltips/tooltips.js"></script>

  <?php
  $g_page_title = $LANG["word_administration"];
  require("$g_root_dir/global/header_code.php");
  ?>

  <style type="text/css">
  #data_tab1 {
    height:26px;
    width: 129px;
    text-align: center;
    background-image: url(<?php if ($curr_tab == 1) echo "$g_root_url/images/tab_selected.jpg"; else echo "$g_root_url/images/tab_unselected.jpg"; ?>);
  }
  #data_tab2 {
    height: 26px;
    width: 129px;
    text-align: center;
    background-image: url(<?php if ($curr_tab == 2) echo "$g_root_url/images/tab_selected.jpg"; else echo "$g_root_url/images/tab_unselected.jpg"; ?>);
  }
  .tabset_underline { border-bottom: 1px solid #b9b9b9; }
  .tabset_between_tabs { border-bottom: 1px solid #b9b9b9; font-size: 4pt; }
  #tab1_content, #tab2_content { padding: 5px; }
  </style>

</head>
<body onload="document.translation_form.translation.focus()">

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($project["name"], "../project.php"),
                  array($LANG["word_translations"], "./"),
                  array($LANG["word_translate"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1>Update Translation</h1>

  <br />

  <?=ot_display_message($success, $message)?>

  <table cellspacing="0" cellpadding="0" summary="tab table" style="width: 100%; margin-bottom: 10px">
  <tr height="26">
    <td width="129" id="data_tab1"><a href="#" onclick="return change_tab(1);">Translate</a></td>
    <td width="2" class="tabset_between_tabs">&nbsp;</td>
    <td width="129" id="data_tab2"><a href="#" onclick="return change_tab(2);">Question?</a></td>
    <td class="tabset_underline" align="right" width="460">&nbsp;</td>
  </tr>
  </table>

  <div id="tab1_content" <?php if ($curr_tab != 1) echo "style=\"display: none;\""; ?>>

    <form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="translation_form">
      <input type="hidden" name="data_id" value="<?=$request['data_id']?>" />
      <input type="hidden" name="data_size" value="<?=$data['data_size']?>" />
      <input type="hidden" name="origin_language_id" value="<?=$project['origin_language_id']?>" />

      <table cellspacing="0" cellpadding="2" width="100%">
      <tr>
        <td valign="top" class="bold pad_right" nowrap width="100">&nbsp;<?=$origin_language_name?></td>
        <td><div style="padding-bottom: 4px"><?=nl2br($data['data'])?></div></td>
      </tr>
      <tr>
        <td valign="top" class="bold pad_right translation_row" nowrap>&nbsp;<?=$target_language_name?></td>
        <td class="translation_row" style="padding-right:8px;">
          <?php
          $textarea_height = "20px";
          if ($data["data_size"] > $g_PHRASE_SIZE)
            $textarea_height = "80px";
          if ($data["data_size"] > $g_SENTENCE_SIZE)
            $textarea_height = "120px";
          if ($data["data_size"] > $g_PARAGRAPH_SIZE)
            $textarea_height = "250px";
          ?>
          <textarea style="width:100%;height:<?=$textarea_height?>" name="translation" id="translation"></textarea>
        </td>
      </tr>
      </table>

			<div style="float:right">
				<div style="padding:3px; text-align:right"><input type="submit" name="translate" value="<?=$LANG['word_translate_arrows']?>" class="blue" /></div>
			</div>

      <br />

      <table cellspacing="0" cellpadding="0" width="100%">
      <tr>
        <td valign="top">

          <table cellspacing="2" cellpadding="1" class="info">
          <tr>
            <td class="blue pad_right" nowrap><?=$LANG["label_last_modified"]?></td>
            <td><?php echo date("M jS, g:i A", ot_convert_datetime_to_timestamp($data["last_modified"]))?></td>
          </tr>
          <?php
          if ($version["show_labels_on_translator_pages"] == "yes") {
          ?>
          <tr>
            <td class="blue pad_right" nowrap><?=$LANG["label_php_label"]?></td>
            <td><?=htmlspecialchars($data['data_label'])?></td>
          </tr>
          <?php } ?>
          <tr>
            <td class="blue pad_right" nowrap><?=$LANG["word_category"]?></td>
            <td><?=ot_get_category_name($data["category_id"])?></td>
          </tr>
          </table>

        </td>
        <td width="60%">

          <?php
          // if there's a comment for the translator, display it in a notification box for emphasis
          if (!empty($data["comments_for_translators"]))
          {
          ?>
            <div class="notify"><span><span><span><span><span><span><span><span>
              <div class="bold"><?=$LANG["label_comments_for_translators"]?></div>
              <p>
                <?=$data["comments_for_translators"]?>
              </p>
            </span></span></span></span></span></span></span></span></div>
          <?php
          }
          ?>

        </td>
      </tr>
      </table>

    </form>

  </div>

  <div id="tab2_content" <?php if ($curr_tab != 2) echo "style=\"display: none;\""; ?>>

    <?php if (empty($data_question_thread)) { ?>

    <div>
      If you have a question about this text, please use the form below to contact the project
      manager. You will be notified of all responses to your questions on the
      <a href="../messages">Messages</a> section on your <a href="../project.php">Project Dashboard</a>.
    </div>

    <br />

    <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
      <input type="hidden" name="project_id" value="<?=$project_id?>" />
      <input type="hidden" name="data_id" value="<?=$data_id?>" />
      <input type="hidden" name="language_id" value="<?=$target_language_id?>" />

      <table cellspacing="0" width="100%">
      <tr>
        <td valign="top" width="15" class="red">*</td>
        <td valign="top" width="120">Subject</td>
        <td>
          <table cellspacing="0" cellpadding="1" width="100%">
          <tr>
            <td width="30"><input type="radio" name="subject" value="context" id="q1" checked /></td>
            <td><label for="q1">In what context is the text used?</label></td>
          </tr>
          <tr>
            <td><input type="radio" name="subject" value="custom" id="q2" /></td>
            <td nowrap><label for="q2">Other:</label> <input type="text" style="width: 530px" name="custom_subject" onkeyup="set_subject_to_other()" /></td>
          </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td> </td>
        <td valign="top">Message</td>
        <td>
          <textarea name="message" id="message" style="width:100%; height:60px"></textarea>
        </td>
      </tr>
      <tr>
        <td colspan="2"> </td>
        <td>
          <input type="submit" name="add_question" class="blue" value=" SEND " />
        </td>
      </tr>
      </table>
    </form>

    <?php } else { ?>

    <table cellpadding="1" cellspacing="0">
    <tr>
    	<td width="120">Subject</td>
    	<td class="italic">
        <?=$data_question_thread[0]["subject"]?>
      </td>
    </tr>
    </table>

    <br />

    <table class="info" width="100%" cellpadding="1" cellspacing="0">
    <?php
    $accounts = array();
    $question_id = $data_question_thread[0]["question_id"];
    for ($i=0; $i<count($data_question_thread); $i++)
    {
      $status = $data_question_thread[$i]["status"];
   	  $creation_date = ot_get_date("", $data_question_thread[$i]["creation_date"], "M jS Y, g:i A");
      $message = $data_question_thread[$i]["message"];
      $account_id = $data_question_thread[$i]["account_id"];

      // if we haven't already asked the database for information on this account, do so now
      if (!array_key_exists($account_id, $accounts))
        $accounts[$account_id] = ot_get_account($account_id);

      // piece together what we want to display
      $account_type = "";
      if ($accounts[$account_id]["account_type"] != "translator")
      {
        $account_type = preg_replace("/_/", " ", $accounts[$account_id]["account_type"]);
        $account_type = ", " . ucwords($account_type);
      }
      $display_name = "{$accounts[$account_id]["first_name"]} {$accounts[$account_id]["last_name"]}$account_type";

      // if the row is unread and NOT written by the translator, highlight it
      $css_class = "";
      if ($status == "unread" && $translator_id != $account_id)
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
            </tr>";

      if (!empty($message))
      {
        echo "
            <tr>
              <td class=\"bold no_underline\">Message</td>
              <td class=\"no_underline\">$message</td>
            </tr>
            ";
      }

      echo "</table>

          </td>
        </tr>";
    }
    ?>
    </table>

    <br />

    <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
      <input type="hidden" name="project_id" value="<?=$project_id?>" />
      <input type="hidden" name="data_id" value="<?=$data_id?>" />
      <input type="hidden" name="question_id" value="<?=$question_id?>" />
      <input type="hidden" name="language_id" value="<?=$target_language_id?>" />

      <div id="comment_box">
        <textarea name="message" id="message" style="width:100%; height: 60px;"></textarea>
      </div>

      <p>
        <input type="submit" name="add_comment" value="Add Comment &raquo;" class="blue bold" />
      </p>

    </form>

    <?php } ?>

  </div>

  <div class="hr"></div>
  <p>
    <a href="index.php"><?=$LANG["label_backtotranslations"]?></a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
<?php

// now mark any "unread" responses as read
if (!empty($question_id))
	ot_mark_data_responses_as_read($translator_id, $question_id);

?>
