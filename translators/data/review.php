<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');

require("_review.php");
?>
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = $LANG["word_administration"];
  require("$g_root_dir/global/header_code.php");
  ?>
  <script type="text/javascript" src="/global/general.js"></script>

  <script type="text/javascript">
  /* <![CDATA[ */
  function select_rating(rating)
  {
    switch (rating)
    {
      case "excellent":
        if ($("edit_translation_message").style.display == "block" || $("edit_translation_message").style.display == "")
        {
          new Effect.Fade($("edit_translation_message"), { duration: 0.7 });
          new Effect.BlindUp($("edit_translation_div"), { duration: 0.7 });
          new Effect.Appear($("display_translation_div"), { delay: 0.7 });
        }
        break;
      case "good":
      case "fair":
      case "poor":
      case "invalid":
        if ($("edit_translation_message").style.display == "none")
        {
          new Effect.Appear($("edit_translation_message"), { duration: 0.7 });
          new Effect.BlindDown($("edit_translation_div"), { duration: 0.7 });
          $("display_translation_div").style.display = "none";
        }
        break;
    }
  }

  function check_form(f)
  {
    var rating = "";
    for (i=0; i<f.rating.length; i++)
    {
      if (f.rating[i].checked)
        rating = f.rating[i].value;
    }

    // if the rating isn't excellent, check the translator improved the translation
    if (rating != "excellent")
    {
      if ($("display_translation_div").innerHTML == f.new_translation.value)
      {
        alert("<?=$LANG['validation_update_translation']?>");
        return false;
      }
    }

    return true;
  }
  /* ]]> */
  </script>
</head>
<body onload="document.translation_form.translation.focus()">

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($project["name"], "../project.php"),
                  array($LANG["word_translations"], "./"),
                  array($LANG["word_review"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["label_review_translation"]?></h1>

  <br />

  <?=ot_display_message($success, $message)?>

  <div id="tab1_content">

    <form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="translation_form" onsubmit="return check_form(this)">

      <table cellspacing="0" cellpadding="2" width="100%">
      <tr>
        <td valign="top" class="bold pad_right" nowrap width="100">&nbsp;<?=$origin_language_name?></td>
        <td><div style="padding-bottom: 4px"><?=nl2br($data['data'])?></div></td>
      </tr>
      <tr>
        <td valign="top" class="bold pad_right translation_row" nowrap>&nbsp;<?=$target_language_name?></td>
        <td valign="top" class="pad_right translation_row">
          <div id="display_translation_div"><?=nl2br($data['translation'])?></div>
          <div id="edit_translation_div" style="display: none;">
          <?php
          $textarea_height = "20px";
          if ($data["data_size"] > $g_PHRASE_SIZE)
            $textarea_height = "80px";
          if ($data["data_size"] > $g_SENTENCE_SIZE)
            $textarea_height = "120px";
          if ($data["data_size"] > $g_PARAGRAPH_SIZE)
            $textarea_height = "250px";
          ?>
          <textarea style="width:100%;height:<?=$textarea_height?>" name="new_translation"><?=$data['translation']?></textarea>
          </div>
        </td>
      </tr>
      </table>

      <br />

      <h3><?=$LANG["label_rate_translation"]?></h3>

      <div style="position: relative">
        <div id="edit_translation_message" style="position: absolute; left: 200px; width: 280px; display:none;">
          <div class="notify"><span><span><span><span><span><span><span><span>
             <div style="margin: 6px;"><?=$LANG["text_please_edit_translation"]?></div>
          </span></span></span></span></span></span></span></span></div>
        </div>

        <table cellspacing="0" cellpadding="0">
        <tr>
          <td>
            <?php ot_display_stars(4); ?>
          </td>
          <td>
            <input type="radio" name="rating" value="excellent" id="rating1" onchange="select_rating(this.value)" /><label for="rating1"><?=$LANG["word_excellent"]?></label>
          </td>
        </tr>
        <tr>
          <td align="right">
            <?php ot_display_stars(3); ?>
          </td>
          <td>
            <input type="radio" name="rating" value="good" id="rating2" onchange="select_rating(this.value)" /><label for="rating2"><?=$LANG["word_good"]?></label>
          </td>
        </tr>
        <tr>
          <td align="right">
            <?php ot_display_stars(2); ?>
          </td>
          <td>
            <input type="radio" name="rating" value="fair" id="rating3" onchange="select_rating(this.value)" /><label for="rating3"><?=$LANG["word_fair"]?></label>
          </td>
        </tr>
        <tr>
          <td align="right">
            <?php ot_display_stars(1); ?>
          </td>
          <td>
            <input type="radio" name="rating" value="poor" id="rating4" onchange="select_rating(this.value)" /><label for="rating4"><?=$LANG["word_poor"]?></label>
          </td>
        </tr>
        <tr>
          <td></td>
          <td>
            <input type="radio" name="rating" value="invalid" id="rating5" onchange="select_rating(this.value)" /><label for="rating5" class="red"><?=$LANG["word_invalid"]?></label>
          </td>
        </tr>
        </table>

        <div style="position: absolute; right: 0px; bottom: 0px;">
          <div style="padding:3px; text-align:right"><input type="submit" name="review" value="<?=$LANG['word_review_arrows']?>" class="blue" /></div>
        </div>

      </div>

    </form>

    <br clear="all" />

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
