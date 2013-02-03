<?php
session_start();
header("Cache-control: private");

require("_index.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = $LANG["word_translators"];
  require("$g_root_dir/global/header_code.php");
  ?>
</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_translators"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["word_translators"]?></h1>

  <p><?=$LANG["text_translator_page_summary"]?></p>

  <table class="info" width="100%" cellpadding="1" cellspacing="0">
  <tr>
    <th width="30" align="center"><?=$LANG["word_id_uc"]?></th>
    <th><?=$LANG["word_translator"]?></th>
    <th><?=$LANG["word_status"]?></th>
    <th align="center"><?=$LANG["word_reliable"]?></th>
    <th align="center"><?=$LANG["word_reviews"]?></th>
    <th align="center"><?=$LANG["word_translations"]?></th>
    <th width="120" align="center"><?=$LANG["label_last_logged_in"]?></th>
	  <th width="70" align="center"><?=$LANG["word_details_uc"]?></th>
	  <th width="70" align="center"><?=$LANG["word_delete_uc"]?></th>
  </tr>

  <?php
  foreach ($translators as $translator)
  {
    $translator_id = $translator["translator_id"];
    $translator_name = "{$translator['first_name']} {$translator['last_name']}";
    $total_percent_reliable = $translator["total_percent_reliable"];
    $total_review_points = $translator["total_review_points"];
    $total_translation_points = $translator["total_translation_points"];
    $status =  "<span class='{$translator["status"]}'>" . ucwords($translator["status"]) . "</span>";

    // format dates
    $last_logged_in = "<span class='light_grey'>{$LANG['label_never_logged_in']}</span>";

    if (!empty($translator["last_logged_in"]))
    	$last_logged_in = ot_get_date("", $translator["last_logged_in"], "M j, g:i A");

    echo "<tr>
            <td align='center' class='blue bold'>$translator_id</td>
            <td>$translator_name</td>
            <td>$status</td>
            <td align='center'>$total_percent_reliable%</td>
            <td align='center'>$total_review_points</td>
            <td align='center'>$total_translation_points</td>
            <td>$last_logged_in</td>
					  <td align='center'><a href='edit.php?translator_id=$translator_id'>{$LANG["word_details_uc"]}</a></td>
					  <td align='center'><a href='delete.php?translator_id=$translator_id'>{$LANG["word_delete_uc"]}</a></td>
          </tr>";
  }
  ?>

  </table>

  <p>
    <form action="new.php" method="post">
      <input type="submit" value="<?=$LANG['label_add_translator']?>" />
  	</form>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
