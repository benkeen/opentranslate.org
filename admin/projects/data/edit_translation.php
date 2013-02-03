<?php
session_start();
header("Cache-control: private");
header("Content-Type: text/html; charset=utf-8");

require("_edit_translation.php");
?>
<html dir="<?=$LANG['direction']?>">
<head>

  <script type="text/javascript" src="/global/tiny_mce/tiny_mce.js"></script>
  <script type="text/javascript">
    tinyMCE.init({
      mode : "<?=$tiny_mce_mode?>",
      elements : "translation",
      theme : "advanced",
      theme_advanced_toolbar_location : "top",
      theme_advanced_buttons1 : "bold,italic,underline,separator,justifyleft,justifycenter,justifyright,separator,bullist,numlist,separator,outdent,indent,separator,forecolor,backcolor,separator,link,unlink,hr,separator,code",
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
        echo 'height: "100",';
      ?>
      content_css : "/global/tinymce.css"
    });
  </script>

  <?php
  $g_page_title = $LANG["word_administration"];
  require("$g_root_dir/global/header_code.php");
  ?>
  <script type="text/javascript" src="/global/general.js"></script>
</head>
<body onload="document.translation_form.translation.focus()">

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_projects"], "$g_root_url/admin/projects/"),
                  array($project["name"], "../project.php"),
                  array($LANG["word_data"], "./"),
                  array($LANG["label_manage_data"], "edit_data.php?data_id=$data_id"),
                  array($LANG["label_translation_history"], "view_history.php?translation_id=$translation_id&data_id=$data_id"),
                  array("Edit Translation", "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1>Edit Translation</h1>

  <br />

  <?=ot_display_message($success, $message)?>

  <div id="tab1_content">

    <form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="translation_form">
      <input type="hidden" name="data_id" value="<?=$data_id?>" />
      <input type="hidden" name="translation_id" value="<?=$translation_id?>" />

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
          <textarea style="width:100%;height:<?=$textarea_height?>" name="translation" id="translation"><?=$translation["translation"]?></textarea>
        </td>
      </tr>
      </table>

      <div style="float:right">
        <div style="padding:3px; text-align:right"><input type="submit" name="update" value="Update Translation >>" class="blue" /></div>
      </div>

      <br />
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

    <div class="hr"></div>
    <p>
      <a href="edit_data.php?data_id=<?=$data_id?>">&laquo; Back to Manage Data</a>
    </p>

  </div>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>

