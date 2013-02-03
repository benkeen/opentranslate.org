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
                  array($LANG["word_projects"], "$g_root_url/admin/projects/"),
                  array($project["name"], "../project.php"),
                  array($LANG["word_translators"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["word_translators"]?></h1>

  <?php if ($num_translators == 0) { ?>

    <br />

    <div class="notify"><span><span><span><span><span><span><span><span>
      <?=$LANG["text_project_no_translators"]?>
    </span></span></span></span></span></span></span></span></div>

  <?php } else { ?>

    <p><?=$LANG["text_project_translators_page_summary"]?></p>

    <?php
    // display page navigation
    ot_display_page_nav($num_translators, $g_max_num_translators_per_page, $current_project_translators_page, "", "proj_translators");
    ?>

    <table class="info" width="100%" cellpadding="1" cellspacing="0">
    <tr>
      <th width="30" align="center"><?=$LANG["word_id_uc"]?></th>
      <th><?=$LANG["word_translator"]?></th>
      <th><?=$LANG["word_status"]?></th>
      <th align="center"><?=$LANG["word_languages"]?></th>
      <th align="center"><?=$LANG["word_reliable"]?></th>
      <th align="center"><?=$LANG["word_reviews"]?></th>
      <th align="center"><?=$LANG["word_translations"]?></th>
      <th width="130" align="center"><?=$LANG["label_last_logged_in"]?></th>
  	  <th width="70" align="center"><?=$LANG["word_details_uc"]?></th>
    </tr>

    <?php

    $languages = array();
    foreach ($translator_ids as $translator_id)
    {
      $translator = ot_get_translator($translator_id);

			// loop through all the languages that this translator speaks, and keep track of the ones that the project
			// is not based in
			$translator_languages = array();
			foreach ($translator["language_ids"] as $language_id)
			{
				if (!array_key_exists($language_id, $languages))
					$languages[$language_id] = ot_get_language_name($language_id);

			  if ($language_id != $origin_language_id)
				  $translator_languages[] = $languages[$language_id];
			}
			$language_str = join("<br />", $translator_languages);

      $translator_name = "{$translator['first_name']} {$translator['last_name']}";
      $total_percent_reliable = $translator["total_percent_reliable"];
      $total_review_points = $translator["total_review_points"];
      $total_translation_points = $translator["total_translation_points"];
      $status =  "<span class='{$translator["status"]}'>" . ucwords($translator["status"]) . "</span>";

      // format dates
      $last_logged_in = "<span class='light_grey'>{$LANG['label_never_logged_in']}</span>";

      if (!empty($translator["last_logged_in"]))
      	$last_logged_in = ot_get_date("", $translator["last_logged_in"], "M jS y, g:i A");

      echo "<tr>
              <td valign='top' align='center' class='blue bold'>$translator_id</td>
              <td valign='top'>$translator_name</td>
              <td valign='top'>$status</td>
              <td>$language_str</td>
              <td valign='top' align='center'>$total_percent_reliable%</td>
              <td valign='top' align='center'>$total_review_points</td>
              <td valign='top' align='center'>$total_translation_points</td>
              <td valign='top'>$last_logged_in</td>
  					  <td valign='top' align='center'><a href='edit.php?translator_id={$translator["translator_id"]}'>{$LANG["word_details_uc"]}</a></td>
            </tr>";
    }
    ?>
    </table>

  <?php } ?>

  <p>
    <input type="button" value="Create Translator Account" onclick="window.location='new.php'" />
  </p>

  <div class="hr"></div>

  <p>
    <a href="../project.php"><?=$LANG["label_backtoproject"]?></a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
