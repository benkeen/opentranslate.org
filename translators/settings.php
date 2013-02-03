<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');

require("_settings.php");
?>
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = "";
  require("$g_root_dir/global/header_code.php");
  ?>
</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "./"),
                  array($project["name"], "project.php?project_id=$project_id"),
                  array($LANG["word_settings"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["word_settings"]?></h1>

  <p><?=$LANG["text_translators_settings_page_summary"]?></p>

  <?=ot_display_message($success, $message)?>

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post" onsubmit="checkSelected(this['selected_translations[]'])">

    <table class="info" width="100%" cellpadding="1" cellspacing="0">
    <tr>
      <td width="20" valign="top" class="red">*</td>
      <td>

        <table cellpadding="0" cellspacing="0" class="list_table" width="100%">
        <tr height="20">
          <td class="th medium_grey" width="140"><?=$LANG["label_translations_needed"]?></td>
          <td class="th" width="120"> </td>
          <td class="th medium_grey" width="500"><?=$LANG["label_selected_translations"]?></td>
        </tr>
        <tr>
          <td width="190" class="no_underline">
            <select name="translations_needed" id="translations_needed" size="6" multiple style="width: 250px;">
              <?php
              foreach ($project["languages"] as $lang_info)
              {
							  $lang_id       = $lang_info["language_id"];
								$language_name = $lang_info["language_name"];

  						  // if this language isn't spoken by the translator, ignore it!
                if (!in_array($lang_id, $translator["language_ids"]))
                  continue;

  	            // if the origin language is the same as the target language, ignore it!
  	            if ($origin_language_id == $lang_id)
  								continue;

  							// if this translator has already signed UP for this language, ignore it!
    						if (key_exists($project_id, $translator_project_languages))
    						{
  							  $has_signed_up = false;
                  foreach ($translator_project_languages[$project_id] as $curr_language_id)
                  {
  								  if ($curr_language_id == $lang_id)
  									{
  									  $has_signed_up = true;
  									  break;
  									}
                  }

  								if ($has_signed_up)
  								  continue;
    						}

                echo "<option value='$lang_id'>$origin_language_name - $language_name</option>\n";
              }
              ?>
            </select>
          </td>
          <td align="center" class="no_underline">
            <input type="button" value="<?=$LANG['word_add_arrow']?>" onclick="moveOptions(this.form.translations_needed, this.form['selected_translations[]']);" /><br />
            <br />
            <input type="button" value="<?=$LANG['word_remove_arrow']?>" onclick="moveOptions(this.form['selected_translations[]'], this.form.translations_needed);" />
          </td>
          <td width="190" class="no_underline">
            <select name="selected_translations[]" size="6" multiple style="width: 250px;">
              <?php
  						if (key_exists($project_id, $translator_project_languages))
  						{
                foreach ($translator_project_languages[$project_id] as $curr_language_id)
                {
  							  $target_language = ot_get_language_name($curr_language_id);
                  echo "<option value='$curr_language_id'>$origin_language_name - $target_language</option>\n";
                }
  						}
              ?>
            </select>
          </td>
        </tr>
        </table>

      </td>
    </tr>
    <tr>
      <td width="20" class="red">*</td>
      <td>
        <input type="checkbox" name="may_credit_translator" id="may_credit_translator"
          <?php if ($translator_project_settings["may_credit_translator"] == "yes") echo "checked"; ?> />
          <label for="may_credit_translator">Yes, you may credit me as a translator of this project</label>
      </td>
    </tr>
    </table>

    <p>
      <input type="submit" name="update_settings" value="<?=$LANG['word_update']?>" />
    </p>

  </form>

  <div class="hr"></div>
  <p>
    <a href="project.php"><?=$LANG["label_backtoproject"]?></a>
  </p>

  <br />


  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">

    <div class="notify" style="width:600px"><span><span><span><span><span><span><span><span>
      <h3><?=$LANG['label_leave_project']?></h3>

      <p>
        <?=$LANG['text_leave_project']?>
      </p>

      <input type="submit" name="leave_project" value="<?=$LANG['label_leave_project']?>" />
    </span></span></span></span></span></span></span></span></div>

  </form>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
