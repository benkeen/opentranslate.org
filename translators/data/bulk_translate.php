<?php
session_start();
header("Cache-control: private");
header("Content-Type: text/html; charset=utf-8");

require("_bulk_translate.php");
?>
<html dir="<?=$LANG['direction']?>">
<head>
  <script type="text/javascript" src="<?=$g_root_url?>/global/tiny_mce/tiny_mce.js"></script>
  <script type="text/javascript">
    tinyMCE.init({
      mode : "exact",
      elements : "<?=$html_fields_str?>",
      theme : "advanced",
      theme_advanced_toolbar_location : "top",
      theme_advanced_buttons1 : "bold,italic,underline,separator,justifyleft,justifycenter,justifyright,separator,bullist,numlist,separator,link,unlink,hr,separator,code",
      theme_advanced_buttons2 : "",
      theme_advanced_buttons3 : "",
      theme_advanced_toolbar_align : "left",
      theme_advanced_path_location : "bottom",
      theme_advanced_resize_horizontal : false,
      theme_advanced_resizing : true,
      height: "100", // for version 1
      content_css : "<?=$g_root_url?>/global/tinymce.css"
    });

    // toggles between details and short view. Whenever this is called, the content of the first
    // view is transfer to the second.
    function change_view(view)
    {
      if (view == "detailed")
      {
        var orig_form = document.translation_form_short;
        var dest_form = document.translation_form_detailed;
        var detailed_display = "block";
        var short_display = "none";
      }
      else
      {
        var orig_form = document.translation_form_detailed;
        var dest_form = document.translation_form_short;
        var detailed_display = "none";
        var short_display = "block";
      }

      data_ids = orig_form.data_ids.value.split(",");
      for (i=0; i<data_ids.length; i++)
        dest_form["translation_" + data_ids[i]].value = orig_form["translation_" + data_ids[i]].value;

      $("detailed_view").style.display = detailed_display;
      $("short_view_link").style.display = detailed_display;

      $("short_view").style.display = short_display;
      $("detailed_view_link").style.display = short_display;
    }

    function ask_question(data_id)
    {
      $("custom_subject").value = "Question regarding item #" + data_id;
      $("question_form_data_id").value = data_id
      $("message").focus();
    }

    function submit_question_form()
    {
      var url = "/global/code/actions.php";

      if (!$("question_form_data_id").value)
      {
        alert("In order to submit a question, click the row's (?) icon to let us know which data item it is regarding.");
        $("question_form_data_id").focus();
        return false;
      }

      $("loading_image").style.display = "block";
    	setTimeout('$("loading_image").src="<?=$g_root_url?>/images/notify_loading.gif"', 200); // for IE

      var data = $("question_form").serialize();
      httpRequest("post", url, true, handleSubmitQuestionResponse, data);
    }

    function handleSubmitQuestionResponse()
    {
      if (g_request.readyState == 4)
      {
        if (g_request.status == 200)
        {
          $("question_response").style.display = "block";
          $("question_response").innerHTML = g_request.responseText;
        }
        else
          alert(g_request.status);

        $("loading_image").style.display = "none";
        $("question_form_data_id").value = "";
        $("custom_subject").value = "";
        $("message").value = "";
      }
    }

    function toggle_short_comments(data_id)
    {
      if ($("short_comments_" + data_id).style.display == "block" || $("short_comments_" + data_id).style.display == "")
        new Effect.Fade("short_comments_" + data_id);
      else
        new Effect.Appear("short_comments_" + data_id);
    }

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
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($project["name"], "../project.php"),
                  array($LANG["word_translations"], "./"),
                  array($LANG["word_translate"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <div style="float:right">
    <div id="detailed_view_link" <?php if ($default_bulk_translate_view == "detailed") echo "style='display: none'"; ?>><a href="javascript:change_view('detailed')">Display Detailed View</a></div>
    <div id="short_view_link" <?php if ($default_bulk_translate_view == "short") echo "style='display: none'"; ?>><a href="javascript:change_view('short')">Display Short View</a></div>
  </div>

  <h1><?=$LANG["label_bulk_translation"]?></h1>

  <br />

  <?=ot_display_message($success, $message)?>

  <div id="tab1_content">

    <div id="detailed_view" <?php if ($default_bulk_translate_view != "detailed") echo "style='display: none'"; ?>>

      <div>
        If you have a question about any of the texts to translate, just click on its <span class="blue"><img src="<?=$g_root_url?>/images/question.jpg" /></span>
        link to populate the question form at the bottom of the page. For general questions, use
        the <a href="../contact.php">Contact Us</a> page.
      </div>

      <br />

      <form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="translation_form_detailed">
        <input type="hidden" name="origin_language_id" value="<?=$project['origin_language_id']?>" />
        <input type="hidden" name="bulk_translate_view" value="detailed" />

        <?php
        $data_ids = array();
        while ($row = mysql_fetch_assoc($search_data))
        {
          $data_ids[] = $row['data_id'];
					
          $trans = ot_get_data_translation($row["data_id"], $target_language_id);
					if (empty($trans))
					{
					  echo "<input type=\"hidden\" name=\"data_action_{$row['data_id']}\" value=\"add\" />";
					}		
					else
					{
					  echo "<input type=\"hidden\" name=\"data_action_{$row['data_id']}\" value=\"update\" />";
					  echo "<input type=\"hidden\" name=\"data_translation_id_{$row['data_id']}\" value=\"{$trans["translation_id"]}\" />";
					}
        ?>
          <input type="hidden" name="data_size_<?=$row['data_id']?>" value="<?=$row['data_size']?>" />

          <table cellspacing="0" cellpadding="2" width="100%">
          <tr>
            <td valign="top" class="bold pad_right" nowrap width="100">&nbsp;<?=$origin_language_name?></td>
            <td>
              <table cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td class="no_underline"><div style="padding-bottom: 4px"><?=nl2br($row['data'])?></div></td>
                <td class="no_underline" width="20"><a href="#question" onclick="ask_question(<?=$row['data_id']?>)"><img src="<?=$g_root_url?>/images/question.jpg" border="0" /></a></td>
              </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td valign="top" class="bold pad_right translation_row" nowrap>&nbsp;<?=$target_language_name?></td>
            <td class="translation_row" style="padding-right:8px;">
              <?php
              $textarea_height = "20px";
              if ($row["data_size"] > $g_PHRASE_SIZE)
                $textarea_height = "80px";
              if ($row["data_size"] > $g_SENTENCE_SIZE)
                $textarea_height = "120px";
              if ($row["data_size"] > $g_PARAGRAPH_SIZE)
                $textarea_height = "250px";
              ?>
              <textarea style="width:100%;height:<?=$textarea_height?>" name="translation_<?=$row['data_id']?>"><?=@$trans["translation"]?></textarea>
            </td>
          </tr>
          </table>

          <br />

          <table cellspacing="0" cellpadding="0" width="100%">
          <tr>
            <td valign="top">

              <table cellspacing="2" cellpadding="1" class="info">
              <tr>
                <td class="blue pad_right" nowrap><?=$LANG["label_last_modified"]?></td>
                <td><?php echo date("M jS Y, g:i A", ot_convert_datetime_to_timestamp($row["last_modified"]))?></td>
              </tr>
              <?php
              if ($version["show_labels_on_translator_pages"] == "yes") {
              ?>
              <tr>
                <td class="blue pad_right" nowrap><?=$LANG["label_php_label"]?></td>
                <td><?=htmlspecialchars($row['data_label'])?></td>
              </tr>
              <?php } ?>
              <tr>
                <td class="blue pad_right" nowrap><?=$LANG["word_category"]?></td>
                <td><?=ot_get_category_name($row["category_id"])?></td>
              </tr>
              </table>

            </td>
            <td width="60%">

              <?php
              // if there's a comment for the translator, display it in a notification box for emphasis
              if (!empty($row["comments_for_translators"]))
              {
              ?>
                <div class="notify"><span><span><span><span><span><span><span><span>
                  <div class="bold"><?=$LANG["label_comments_for_translators"]?></div>
                  <p>
                    <?=$row["comments_for_translators"]?>
                  </p>
                </span></span></span></span></span></span></span></span></div>
              <?php
              }
              ?>

            </td>
          </tr>
          </table>

          <div class="hr"></div>
          <br />

          <?php
        }
        echo "<input type='hidden' name='data_ids' value='" . join(",", $data_ids) . "' />\n";
        ?>

        <div style="float:right">
          <input type="submit" name="translate" value="<?=$LANG['word_translate_arrows']?>" class="blue" />
        </div>

        <br />

      </form>
    </div>

    <div id="short_view" <?php if ($default_bulk_translate_view != "short") echo "style='display: none'"; ?>>

      <div> <!-- style="line-height: 21px;"-->
        If you have a question about any of the texts to translate, just click on its <img src="<?=$g_root_url?>/images/question.jpg" style="margin-bottom:-4px;"/>
        link to populate the question form at the bottom of the page. For general questions, use
        the <a href="../contact.php">Contact Us</a> page. If the data has a yellow star <span><img src="<?=$g_root_url?>/images/has_comments_star.jpg" style="margin-bottom:-4px;" /></span>,
        the project manager has provided additional information on how to translate the item.
      </div>

      <br />

      <form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="translation_form_short">
        <input type="hidden" name="origin_language_id" value="<?=$project['origin_language_id']?>" />
        <input type="hidden" name="bulk_translate_view" value="short" />

        <table cellspacing="0" cellpadding="1" width="100%" class="info">
        <tr>
          <th class="pad_right" nowrap width="40%">&nbsp;<?=$origin_language_name?></th>
          <th class="pad_right" nowrap width="60%">&nbsp;<?=$target_language_name?></th>
        </tr>
        <?php
        mysql_data_seek($search_data, 0);
        while ($row = mysql_fetch_assoc($search_data))
        {
        ?>
				
          <tr>
            <td valign="top"><div style="padding-bottom: 4px"><?=nl2br($row['data'])?></div></td>
            <td style="padding-right:8px;">
              <?php
              $trans = ot_get_data_translation($row["data_id"], $target_language_id);
    					if (empty($trans))
    					{
    					  echo "<input type=\"hidden\" name=\"data_action_{$row['data_id']}\" value=\"add\" />";
    					}		
    					else
    					{
    					  echo "<input type=\"hidden\" name=\"data_action_{$row['data_id']}\" value=\"update\" />";
    					  echo "<input type=\"hidden\" name=\"data_translation_id_{$row['data_id']}\" value=\"{$trans["translation_id"]}\" />";
    					}
							
              $textarea_height = "20px";
              if ($row["data_size"] > $g_PHRASE_SIZE)
                $textarea_height = "80px";
              if ($row["data_size"] > $g_SENTENCE_SIZE)
                $textarea_height = "120px";
              if ($row["data_size"] > $g_PARAGRAPH_SIZE)
                $textarea_height = "250px";

              $comments_img_link = "<img src='$g_root_url/images/has_comments_star_grey.jpg' />";
              if (!empty($row["comments_for_translators"]))
                $comments_img_link = "<a href='#question' onclick='toggle_short_comments({$row['data_id']})'><img src='$g_root_url/images/has_comments_star.jpg' border='' /></a>";
              ?>
              <table cellspacing="0" cellpadding="0" width="100%">
              <tr>
                <td class="no_underline">
                  <textarea style="width:100%;height:<?=$textarea_height?>" name="translation_<?=$row['data_id']?>"><?=@$trans["translation"]?></textarea>
                  <input type="hidden" name="data_size_<?=$row['data_id']?>" value="<?=$row['data_size']?>" />
                </td>
                <td class="no_underline" width="22" valign="top" align="right"><?=$comments_img_link?></td>
                <td class="no_underline" width="22" valign="top" align="right"><a href="#question" onclick="ask_question(<?=$row['data_id']?>)"><img src="<?=$g_root_url?>/images/question.jpg" border="0" /></a></td>
              </tr>
              </table>

              <div id="short_comments_<?=$row['data_id']?>" style="display:none">
              <?php
                // if there's a comment for the translator, display it in a notification box for emphasis
                if (!empty($row["comments_for_translators"]))
                {
                ?>
                  <div class="notify"><span><span><span><span><span><span><span><span>
                    <div class="bold"><?=$LANG["label_comments_for_translators"]?></div>
                    <p>
                      <?=$row["comments_for_translators"]?>
                    </p>
                  </span></span></span></span></span></span></span></span></div>
                <?php
                }
                ?>
              </div>

            </td>
          </tr>

          <?php
        }

        echo "</table><input type='hidden' name='data_ids' value='" . join(",", $data_ids) . "' />\n<br />";
        ?>

        <div style="float:right">
          <input type="submit" name="translate" value="<?=$LANG['word_translate_arrows']?>" class="blue" />
        </div>

        <br />

      </form>

      <script type="text/javascript">
      <?php
      // set the focus
      if ($default_bulk_translate_view == "detailed")
        echo "document.translation_form_detailed.translation_{$data_ids[0]}.focus()";
      else
        echo "document.translation_form_short.translation_{$data_ids[0]}.focus()";
      ?>
      </script>

    </div>

    <div class="hr" style="margin-bottom: 8px;"></div>

    <a name="question"></a>

    <div class="notify"><span><span><span><span><span><span><span><span>

      <form action="javascript:void%200" name="question_form" id="question_form" method="post" onsubmit="return submit_question_form()">
        <input type="hidden" name="project_id" value="<?=$project_id?>" />
        <input type="hidden" name="data_id" id="question_form_data_id" value="" />
        <input type="hidden" name="translator_id" value="<?=$translator_id?>" />
        <input type="hidden" name="language_id" value="<?=$target_language_id?>" />
        <input type="hidden" name="action" value="data_question" />
        <input type="hidden" name="subject" value="" />

        <div style="float:right; display:none;" id="loading_image"><img src="<?=$g_root_url?>/images/notify_loading.gif" /></div>
        <h3>Questions</h3>

        <div id="question_response" style="background-color: #FFE79D; padding: 5px; margin-bottom: 4px; border: 1px solid #666666; display:none;"></div>

        <table cellspacing="0" cellpadding="0" width="100%">
        <tr>
          <td width="120">Subject</td>
          <td><input type="text" style="width: 100%" name="custom_subject" id="custom_subject" /></td>
        </tr>
        <tr>
          <td>Message</td>
          <td><textarea name="message" id="message" style="width:100%; height: 60px;"></textarea></td>
        </tr>
        <tr>
          <td></td>
          <td><input type="submit" name="add_comment" value="Submit &raquo;" class="blue bold" /></td>
        </tr>
        </table>

      </form>
    </span></span></span></span></span></span></span></span></div>

    <div class="hr"></div>

    <p>
      <a href="index.php"><?=$LANG["label_backtotranslations"]?></a>
    </p>

  </div>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>