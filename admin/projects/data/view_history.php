<?php
session_start();
header("Cache-control: private");

require("_view_history.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = $LANG["word_administration"];
  require("$g_root_dir/global/header_code.php");
  ?>
  <script type="text/javascript" src="/global/general.js"></script>

  <script type="text/javascript">
  function delete_translation_history(data_id, translation_id)
  {
    var answer = confirm("<?=$LANG['validation_confirm_delete_translation_history']?>");

    if (answer)
    {
      window.location = "<?=$_SERVER['PHP_SELF']?>?data_id=" + data_id + "&translation_id=" + translation_id + "&delete_translation_history=1";
    }
  }
  function set_translation_as_current(translation_id, data_id, translation_history_id)
  {

  }
  </script>
</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_projects"], "$g_root_url/admin/projects/"),
                  array($project["name"], "../project.php"),
                  array($LANG["word_data"], "./"),
                  array($LANG["label_manage_data"], "edit_data.php?data_id=$data_id"),
                  array($LANG["label_translation_history"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["label_translation_history"]?></h1>

  <br />
  <?=ot_display_message($success, $message);?>

  <table cellspacing="2" cellpadding="1">
  <tr>
    <td class="pad_right bold" valign="top" nowrap><?=$LANG["label_original_text"]?></td>
    <td class="blue"><?=$data["data"]?></td>
  </tr>
  </table>

  <br />

  <?php
  $count = 1;
  $original_num_reviews = count($translation_reviews); // used to know when to display (if necessary) the "this translation has had no reviews" message
  foreach ($translation_history as $row)
  {
    $is_most_recent_translation = (count($translation_history) == $count) ? true : false;
    $translation_history_id = $row["translation_history_id"];

  ?>
    <table cellspacing="2" cellpadding="1" class="info" width="100%">
    <tr>
      <td colspan="2" class="no_underline" style="background-color: #E0F4FF; padding: 3px;">

        <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
          <input type="hidden" name="translation_id" value="<?=$request['translation_id']?>" />
          <input type="hidden" name="data_id" value="<?=$request['data_id']?>" />
          <input type="hidden" name="translation_history_id" value="<?=$translation_history_id?>" />

          <span style='float:right'>
            <?php
            if ($is_most_recent_translation)
            {
            ?>
              <input type="submit" name="delete_translation" value="<?=$LANG['label_delete_translation']?>" class="burgundy" />
              <input type="button" value="<?=$LANG['label_delete_and_blacklist']?>" class="burgundy" />
            <?php
            }
            else
            {
//              <input type="submit" name="set_translation_as_current" value="{$LANG['label_set_translation_as_current']}" />
            }
            ?>
          </span>
        </form>

        <span class="bold"><?=$count?></span>.
        <?php echo date("M jS, g:i A", ot_convert_datetime_to_timestamp($row["change_date"]))?>
      </td>
    </tr>
    <tr>
      <td valign="top" width="100" class='pad_right'><?=$LANG["word_translation"]?></td>
      <td><?=$row["translation"]?></td>
    </tr>
    <tr>
      <td valign="top"><?=$LANG["word_translator"]?></td>
      <td>
        <?php
        $translator_info = ot_get_translator($row["account_id"]);
        echo "<div>
               <a href='../translators/edit.php?translator_id={$translator_info['translator_id']}'>{$translator_info['first_name']} {$translator_info['last_name']}</a>,
               {$LANG['word_reliability_c']} <b>{$translator_info['total_percent_reliable']}%</b>,
               {$LANG['label_translation_points']} <b>{$translator_info['total_translation_points']}</b>
             </div>
             ";
        ?>
      </td>
    </tr>
    <?php if ($row["reason_for_change"] != "new") { ?>
    <tr>
      <td valign="top"><?=$LANG["label_reason_for_change"]?></td>
      <td><?=$row["reason_for_change"]?></td>
    </tr>
    <?php } ?>
    <tr>
      <td valign="top"><?=$LANG["word_review_or_reviews"]?></td>
      <td>

        <table cellspacing="1" cellpadding="1">
        <?php

        // this is a bit confusing, and could definitely be improved later. What we're doing here
        // is displaying all reviews on each translation, so the administrator can see all the
        // activity on a translation in chronological order. There's no direct mapping of
        // reviews to translation versions since only one copy of a translation is stored in the
        // database at one time. So, in order to show the sequential activity, we display
        // all reviews UP UNTIL THE FIRST NON-EXCELLENT REVIEW. As soon as a non-excellent review
        // is given, that indicates a new translation must have been made. So, any time we display
        // a review, we shift it off the top of $translation_reviews so it's not displayed in the
        // next loop

        $num_reviews_to_shift = 0;
        foreach ($translation_reviews as $review)
        {
          $reviewer_info = ot_get_translator($review["translator_id"]);

				$num_stars = 0;
				switch ($review["review"])
				{
					case "excellent":
						$num_stars = 4;
						$display_text = $LANG["word_excellent"];
						break;
					case "good":
						$num_stars = 3;
						$display_text = $LANG["word_good"];
						break;
					case "fair":
						$num_stars = 2;
						$display_text = $LANG["word_fair"];
						break;
					case "poor":
						$num_stars = 1;
						$display_text = $LANG["word_poor"];
						break;
					case "invalid":
						$display_text = "<span class='red'>{$LANG['word_invalid']}</span>";
						break;
				}

          echo "<tr>
              <tr>
                <td class='pad_right no_underline'><a href='../translators/edit.php?translator_id={$reviewer_info['translator_id']}'>{$reviewer_info['first_name']} {$reviewer_info['last_name']}</a></td>
                <td class='pad_right no_underline'>";

            ot_display_stars($num_stars);

            echo "</td>
                <td class='no_underline'>$display_text</td>
             </tr>";

          $num_reviews_to_shift++;
        }

        for ($i=0; $i<$num_reviews_to_shift; $i++)
          array_shift($translation_reviews);

        ?>
        </table>

        <?php
        // if this if the LAST row (i.e. the most recent translation) and there have been no reviews for it,
        // let the user know
        if ($is_most_recent_translation && $num_reviews_to_shift == 0)
          echo $LANG["label_translation_not_reviewed"];
        ?>

      </td>
    </tr>
    </table>

    <br />

    <?php
    $count++;
  }
  ?>

  <hr size="1" />

  <br />

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
    <input type="hidden" name="translation_id" value="<?=$request['translation_id']?>" />
    <input type="hidden" name="data_id" value="<?=$request['data_id']?>" />

    <div class="notify">
      <span><span><span><span><span><span><span><span>
        <table cellspacing="1" cellpadding="1">
        <tr>
          <td class="pad_right bold"><?=$LANG["word_summary"]?></td>
          <td>

          <?php
          $translation_complete = false;
          if ($translation["translation_status"] == "completed" || empty($project["trust_threshold"]))
          {
            echo $LANG["label_translation_complete"];
            $translation_complete = true;
          }
          else
          {
            $reviews_needed = $project["trust_threshold"] - $translation["review_count"];
            $parsed_str = preg_replace("/%%x%%/", $reviews_needed, $LANG["label_requires_more_reviews"]);
            echo $parsed_str;
          }
          ?>
          </td>
        </tr>
        <?php
        if (!empty($translation["approval_override_account_id"])) {?>
        <tr>
          <td>Approved:</td>
          <td>
            This translation was manually approved by an administrator / project manager on
            <b><?php echo date("M jS, g:i A", convert_datetime_to_timestamp($translation["approval_override_date"]))?></b>
          </td>
        </tr>
        <?php } ?>
        <tr>
          <td class="pad_right"><?=$LANG["word_actions"]?></td>
          <td>
            <?php
            if (!$translation_complete)
              echo "<input type='submit' name='approve_most_recent_translation' value='{$LANG['label_approve_most_recent_translation']}' />";
            ?>

            <input type="button" value="<?=$LANG['label_delete_translation_history']?>" class="burgundy" onclick="delete_translation_history(<?=$data_id?>, <?=$translation_id?>)" />
            <input type="button" value="Edit Translation" class="blue" onclick="window.location='edit_translation.php?data_id=<?=$data_id?>&translation_id=<?=$translation_id?>'" />
          </td>
        </tr>
        </table>
      </span></span></span></span></span></span></span></span>
    </div>

  </form>

  <p>
    <a href="edit_data.php?data_id=<?=$data_id?>"><?=$LANG["label_back_manage_data_arrows"]?></a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>