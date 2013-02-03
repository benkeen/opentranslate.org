<?php
session_start();
header("Cache-control: private");
header("Content-Type: text/html; charset=utf-8");

require("_bulk_review.php");
?>
<html dir="<?=$LANG['direction']?>">
<head>
  <script type="text/javascript" src="/global/tiny_mce/tiny_mce.js"></script>
  <script type="text/javascript">
  /* <![CDATA[ */
  tinyMCE.init({
    mode : "exact",
    elements : "<?=$html_fields_str?>",
    theme : "advanced",
    theme_advanced_toolbar_location : "top",
    theme_advanced_buttons1 : "bold,italic,underline,separator,justifyleft,justifycenter,justifyright,separator,bullist,numlist,separator,outdent,indent,separator,forecolor,backcolor,separator,link,unlink,hr,separator,code",
    theme_advanced_buttons2 : "",
    theme_advanced_buttons3 : "",
    theme_advanced_toolbar_align : "left",
    theme_advanced_path_location : "bottom",
    theme_advanced_resize_horizontal : false,
    theme_advanced_resizing : true,
    height: "100", // for version 1
    content_css : "/global/tinymce.css"
  });
  </script>

  <?php
  $g_page_title = $LANG["word_administration"];
  require("$g_root_dir/global/header_code.php");
  ?>

  <script type="text/javascript">
  /* <![CDATA[ */

  var g_current_selected = [];
  var g_map = new Array();
  g_map["excellent"] = "edit";
  g_map["good"]      = "no_edit";
  g_map["fair"]      = "no_edit";
  g_map["poor"]      = "no_edit";
  g_map["invalid"]   = "no_edit";

  function select_rating(data_id, rating)
  {
    if (g_current_selected[data_id] == undefined)
      g_current_selected[data_id] = "";

    // if nothing's changed (e.g. going to or from "excellent"), do nothing
    if (g_map[rating] == g_current_selected[data_id])
      return;

    g_current_selected[data_id] = g_map[rating];

    if (g_current_selected[data_id] == "edit")
    {
      $("edit_translation_" + data_id + "_div").style.display = "none";
      $("display_translation_" + data_id + "_div").style.display = "block";
    }
    else
    {
      $("edit_translation_" + data_id + "_div").style.display = "block";
      $("display_translation_" + data_id + "_div").style.display = "none";
    }
  }

  function check_form(f)
  {
    var data_ids = $("data_ids").value.split(",");

    for (j=0; j<data_ids.length; j++)
    {
      var current_data_id = data_ids[j];

      var rating = "";
      for (i=0; i<f["rating_" + current_data_id].length; i++)
      {
        if (f["rating_" + current_data_id][i].checked)
          rating = f["rating_" + current_data_id][i].value;
      }

      // if the rating isn't excellent, check the translator improved the translation
      if (rating != "excellent")
      {
        if ($("display_translation_" + current_data_id + "_div").innerHTML == f["new_translation_" + current_data_id].value)
        {
          row = j+1;
          alert("<?=$LANG['validation_update_translation']?> (" + row + ")");
          return false;
        }
      }
    }

    return true;
  }
  /* ]]> */
  </script>

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

  <h1><?=$LANG["label_bulk_review"]?></h1>

  <br />

  <?=ot_display_message($success, $message)?>

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="translation_form" onsubmit="return check_form(this)">
    <input type="hidden" name="origin_language_id" value="<?=$project['origin_language_id']?>" />

    <?php
    $data_ids = array();
    $count = 1;
    while ($row = mysql_fetch_assoc($search_data))
    {
      $data_id = $row['data_id'];

      // keep track of all data IDs
      $data_ids[] = $data_id;
    ?>
      <input type="hidden" name="data_size_<?=$data_id?>" value="<?=$row['data_size']?>" />

      <table cellspacing="0" cellpadding="2" width="100%">
      <tr>
        <td class="bold black" valign="top" rowspan="2" width="15"><?=$count?>.</td>
        <td valign="top" class="bold medium_grey pad_right" nowrap width="100">&nbsp;<?=$origin_language_name?></td>
        <td><div style="padding-bottom: 4px"><?=nl2br($row['data'])?></div></td>
      </tr>
      <tr>
        <td valign="top" class="bold medium_grey pad_right translation_row" nowrap>&nbsp;<?=$target_language_name?></td>
        <td class="translation_row" style="padding-right:8px;">
          <div id="display_translation_<?=$data_id?>_div"><?=nl2br($row['translation'])?></div>
          <div id="edit_translation_<?=$data_id?>_div" style="display: none;">
            <?php
            $textarea_height = "20px";
            if ($row["data_size"] > $g_PHRASE_SIZE)
              $textarea_height = "80px";
            if ($row["data_size"] > $g_SENTENCE_SIZE)
              $textarea_height = "120px";
            if ($row["data_size"] > $g_PARAGRAPH_SIZE)
              $textarea_height = "250px";
            ?>
            <textarea style="width:100%;height:<?=$textarea_height?>" name="new_translation_<?=$data_id?>"><?=$row['translation']?></textarea>
          </div>
        </td>
      </tr>
      </table>

      <br />

      <table cellspacing="0" cellpadding="0" width="100%">
      <tr>
        <td valign="top">

          <h3><?=$LANG["label_rate_translation"]?></h3>

          <div>

            <table cellspacing="0" cellpadding="0">
            <tr>
              <td>
                <?php ot_display_stars(4); ?>
              </td>
              <td>
                <input type="radio" name="rating_<?=$data_id?>" value="excellent" id="rating<?=$data_id?>_1"
                  onchange="select_rating(<?=$data_id?>, this.value)" /><label for="rating<?=$data_id?>_1"><?=$LANG["word_excellent"]?></label>
              </td>
            </tr>
            <tr>
              <td align="right">
                <?php ot_display_stars(3); ?>
              </td>
              <td>
                <input type="radio" name="rating_<?=$data_id?>" value="good" id="rating<?=$data_id?>_2"
                  onchange="select_rating(<?=$data_id?>, this.value)" /><label for="rating<?=$data_id?>_2"><?=$LANG["word_good"]?></label>
              </td>
            </tr>
            <tr>
              <td align="right">
                <?php ot_display_stars(2); ?>
              </td>
              <td>
                <input type="radio" name="rating_<?=$data_id?>" value="fair" id="rating<?=$data_id?>_3"
                  onchange="select_rating(<?=$data_id?>, this.value)" /><label for="rating<?=$data_id?>_3"><?=$LANG["word_fair"]?></label>
              </td>
            </tr>
            <tr>
              <td align="right">
                <?php ot_display_stars(1); ?>
              </td>
              <td>
                <input type="radio" name="rating_<?=$data_id?>" value="poor" id="rating<?=$data_id?>_4"
                  onchange="select_rating(<?=$data_id?>, this.value)" /><label for="rating<?=$data_id?>_4"><?=$LANG["word_poor"]?></label>
              </td>
            </tr>
            <tr>
              <td></td>
              <td>
                <input type="radio" name="rating_<?=$data_id?>" value="invalid" id="rating<?=$data_id?>_5"
                  onchange="select_rating(<?=$data_id?>, this.value)" /><label for="rating<?=$data_id?>_5" class="red"><?=$LANG["word_invalid"]?></label>
              </td>
            </tr>
            </table>

          </div>

        </td>
        <td width="60%">

          <table cellspacing="2" cellpadding="1" class="info">
          <tr>
            <td class="blue pad_right" nowrap><?=$LANG["label_last_modified"]?></td>
            <td><?php echo date("M jS, g:i A", ot_convert_datetime_to_timestamp($row["last_modified"]))?></td>
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

      $count++;
    }
    echo "<input type='hidden' id='data_ids' name='data_ids' value='" . join(",", $data_ids) . "' />\n";
    ?>

    <div style="float:right">
      <input type="submit" name="translate" value="<?=$LANG['word_review_arrows']?>" class="blue" />
    </div>

    <br />

  </form>

  <p>
    <a href="index.php"><?=$LANG["label_backtotranslations"]?></a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>

