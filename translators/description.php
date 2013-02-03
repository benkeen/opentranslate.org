<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');

require("_description.php");
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
                  array($LANG["word_dashboard"], "$g_root_url/translators/"),
                  array($LANG["label_project_description"] . ": " . $project["name"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");

?>

  <h1><?=$project['name']?></h1>

  <div><?=$project['description']?></div>

  <div class="hr"></div>

  <br />

	<form action="<?=$_SERVER['PHP_SELF']?>" method="post" onsubmit="checkSelected(this['selected_translations[]'])">
	  <input type="hidden" name="project_id" value="<?=$project_id?>" />

    <div class="notify" style="width: 620px"><span><span><span><span><span><span><span><span>
      <div class="bold"><?=$LANG["label_project_sign_up"]?></div>

      <table cellpadding="0" cellspacing="0" class="list_table">
      <tr height="20">
        <td class="th medium_grey" width="200"><?=$LANG["label_translations_needed"]?></td>
        <td class="th" width="120"> </td>
        <td class="th medium_grey" width="200"><?=$LANG["label_selected_translations"]?></td>
				<td width="100" rowspan="2">
				  <input type="submit" name="join" value="<?=$LANG['label_signup_uc']?>" class="bold blue" />
				</td>
      </tr>
      <tr>
        <td width="160">
          <select name="translations_needed" id="translations_needed" size="6" multiple style="width: 200px;">
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
        <td align="center">
          <input type="button" value="<?=$LANG['word_add_arrow']?>" onclick="moveOptions(this.form.translations_needed, this.form['selected_translations[]']);" /><br />
          <br />
          <input type="button" value="<?=$LANG['word_remove_arrow']?>" onclick="moveOptions(this.form['selected_translations[]'], this.form.translations_needed);" />
        </td>
        <td width="160">
          <select name="selected_translations[]" size="6" multiple style="width: 200px;">
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

    </span></span></span></span></span></span></span></span></div>

  </form>

  <p>
    <a href="index.php"><?=$LANG["label_backtodashboard"]?></a>
  </p>


<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>