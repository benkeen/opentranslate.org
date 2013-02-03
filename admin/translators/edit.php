<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');

require("_edit.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = $LANG["label_edit_translator"];
  require("$g_root_dir/global/header_code.php");
  ?>
  <script type="text/javascript" src="/global/scriptaculous/lib/general.js"></script>
  <script type="text/javascript" src="/global/scriptaculous/lib/prototype.js"></script>
  <script type="text/javascript" src="/global/scriptaculous/src/scriptaculous.js"></script>

  <script type="text/javascript">
  /* <![CDATA[ */

  var selected_project_suffixes = new Array();

  // called when submitting the second "Projects" tab
  function update_selected_projects(f)
  {
    // note we have to use "j" not "i", since it's used in checkSelected (!)
    for (j=0; j<selected_project_suffixes.length; j++)
      checkSelected(f["selected_projects" + selected_project_suffixes[j] + "[]"]);
  }
  var current_tab = <?=$curr_tab?>;
  /* ]]> */
  </script>

  <style type="text/css">
  #data_tab1 {
    height:26px;
    text-align: center;
    background-image: url(<?php if ($curr_tab == 1) echo '../../images/tab_selected.jpg'; else echo '../../images/tab_unselected.jpg'; ?>);
  }
  #data_tab2 {
    height: 26px;
    text-align: center;
    background-image: url(<?php if ($curr_tab == 2) echo '../../images/tab_selected.jpg'; else echo '../../images/tab_unselected.jpg'; ?>);
  }
  #data_tab3 {
    height: 26px;
    text-align: center;
    background-image: url(<?php if ($curr_tab == 3) echo '../../images/tab_selected.jpg'; else echo '../../images/tab_unselected.jpg'; ?>);
  }
  .tabset_underline { border-bottom: 1px solid #b9b9b9; }
  #data_tab1 a, #data_tab2 a, #data_tab3 a { display: block; }
  #tab1_content { padding: 5px; }
  #tab2_content { padding: 5px; }
  #tab3_content { padding: 5px; }
  </style>

</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_translators"], "index.php"),
                  array($LANG["label_edit_translator"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["word_translator"]?>: <?=$translator['first_name']?> <?=$translator['last_name']?></h1>
  <br />

  <?=ot_display_message($success, $message);?>

  <table cellspacing="0" cellpadding="0" summary="tab table" style="width: 100%; margin-bottom: 10px">
  <tr height="26">
    <td width="129" id="data_tab1"><a href="#" onclick="return change_tab(1);"><?=$LANG['word_main']?></a></td>
    <td width="2" class="tabset_underline"> </td>
    <td width="129" id="data_tab2"><a href="#" onclick="return change_tab(2);"><?=$LANG['word_statistics']?></a></td>
    <td width="2" class="tabset_underline"> </td>
    <td width="129" id="data_tab3"><a href="#" onclick="return change_tab(3);"><?=$LANG['word_projects']?></a></td>
    <td width="329" class="tabset_underline" align="<?=$LANG['align2']?>"> </td>
  </tr>
  </table>

  <div id="tab1_content" <?php if ($curr_tab != 1) echo "style=\"display: none;\""; ?>>

    <div style="padding: 5px">

      <form action="<?=$_SERVER['PHP_SELF']?>" method="post" onsubmit="checkSelected(this['selected_languages[]']);">
        <input type="hidden" name="translator_id" value="<?=$translator_id?>" />

        <h3><?=$LANG["label_account_info"]?></h3>

        <table class="info" width="600" cellpadding="1" cellspacing="0">
        <tr>
          <td width="180"><?=$LANG["word_status"]?></td>
          <td>
            <input type="radio" name="status" value="active" id="status1"   <?php if ($translator["status"] == "active") echo "checked"; ?> /><label for="status1" class="active"><?=$LANG["word_active"]?></label>
            <input type="radio" name="status" value="disabled" id="status2" <?php if ($translator["status"] == "disabled") echo "checked"; ?> /><label for="status2" class="disabled"><?=$LANG["word_disabled"]?></label>
            <input type="radio" name="status" value="pending" id="status3"  <?php if ($translator["status"] == "pending") echo "checked"; ?> /><label for="status3" class="pending"><?=$LANG["word_pending"]?></label>
            <input type="radio" name="status" value="blacklisted" id="status4" <?php if ($translator["status"] == "blacklisted") echo "checked"; ?> /><label for="status4"><?=$LANG["word_blacklisted"]?></label>
          </td>
        </tr>
        <tr>
          <td><?=$LANG["label_ui_language"]?></td>
          <td>
            <select name="ui_language_id">
              <option value=""><?=$LANG["label_please_select"]?></option>
              <?php
              foreach ($languages as $language)
              {
                $selected = "";
                if ($translator['ui_language_id'] == $language["language_id"])
                  $selected = "selected";

                echo "<option value='{$language['language_id']}' $selected>{$language['language_name']}</option>\n";
              }
              ?>
            </select>
          </td>
        </tr>
        <tr>
          <td><?=$LANG["label_first_name"]?></td>
          <td><input type="text" name="first_name" value="<?=$translator['first_name']?>" /></td>
        </tr>
        <tr>
          <td><?=$LANG["label_last_name"]?></td>
          <td><input type="text" name="last_name" value="<?=$translator['last_name']?>" /></td>
        </tr>
        <tr>
          <td valign="top"><?=$LANG["label_languages_spoken"]?></td>
          <td>

            <table cellpadding="0" cellspacing="0" class="list_table">
            <tr height="20">
              <td class="th medium_grey no_underline" width="140">&nbsp;<?=$LANG["label_available_languages"]?></td>
              <td class="th no_underline" width="120"> </td>
              <td class="th medium_grey no_underline" width="200">&nbsp;<?=$LANG["label_selected_languages"]?></td>
            </tr>
            <tr>
              <td width="135" class="no_underline">
                <select name="available_languages" id="available_languages" size="6" multiple style="width: 130px;">
                  <?php
                  foreach ($languages as $language_info)
                  {
                    if (in_array($language_info["language_id"], $translator["language_ids"]))
                      continue;

                    echo "<option value='{$language_info['language_id']}'>{$language_info['language_name']}</option>\n";
                  }
                  ?>
                </select>
              </td>
              <td align="center" class="no_underline">
                <input type="button" value="<?=$LANG['word_add_arrow_uc']?>" onclick="moveOptions(this.form.available_languages, this.form['selected_languages[]']);" /><br />
                <br />
                <input type="button" value="<?=$LANG['word_remove_arrow_uc']?>" onclick="moveOptions(this.form['selected_languages[]'], this.form.available_languages);" />
              </td>
              <td width="135" class="no_underline">
                <select name="selected_languages[]" size="6" multiple style="width: 130px;">
                  <?php
                  foreach ($languages as $language_info)
                  {
                    if (!in_array($language_info["language_id"], $translator["language_ids"]))
                      continue;

                    $language_id   = $language_info["language_id"];
                    $language_name = $language_info["language_name"];
                    echo "<option value='$language_id'>$language_name</option>\n";
                  }
                  ?>
                </select>
              </td>
            </tr>
            </table>

          </td>
        </tr>
        <tr>
          <td width="180"><?=$LANG["label_num_data_per_page"]?></td>
          <td>
            <select name="ui_num_data_per_page">
              <option value=""><?=$LANG["label_please_select"]?></option>
              <option value="5"  <?php if ($translator["ui_num_data_per_page"] == "5") echo "selected"; ?>>5</option>
              <option value="10" <?php if ($translator["ui_num_data_per_page"] == "10") echo "selected"; ?>>10</option>
              <option value="15" <?php if ($translator["ui_num_data_per_page"] == "15") echo "selected"; ?>>15</option>
              <option value="20" <?php if ($translator["ui_num_data_per_page"] == "20") echo "selected"; ?>>20</option>
              <option value="25" <?php if ($translator["ui_num_data_per_page"] == "25") echo "selected"; ?>>25</option>
              <option value="30" <?php if ($translator["ui_num_data_per_page"] == "30") echo "selected"; ?>>30</option>
              <option value="40" <?php if ($translator["ui_num_data_per_page"] == "40") echo "selected"; ?>>40</option>
              <option value="50" <?php if ($translator["ui_num_data_per_page"] == "50") echo "selected"; ?>>50</option>
            </select>
          </td>
        </tr>
        <tr>
          <td width="180">Default Bulk Translate View</td>
          <td>
            <select name="default_bulk_translate_view">
              <option value="detailed"  <?php if ($translator["default_bulk_translate_view"] == "detailed") echo "selected"; ?>>Detailed</option>
              <option value="short" <?php if ($translator["default_bulk_translate_view"] == "short") echo "selected"; ?>>Short</option>
            </select>
          </td>
        </tr>
        </table>

        <br />
        <h3><?=$LANG["label_login_info"]?></h3>

        <table class="info" width="600" cellpadding="1" cellspacing="0">
        <tr>
          <td width="180"><?=$LANG["word_email"]?></td>
          <td><input type="text" name="email" style="width: 200px" value="<?=$translator['email']?>" /></td>
        </tr>
        <tr>
          <td width="180"><?=$LANG["word_password"]?></td>
          <td>
            <input type="password" name="password" style="width: 100px" value="<?=$translator['password']?>" />
            <input type="button" value="<?=$LANG['word_view']?>" onclick="alert(this.form.password.value);"/>
          </td>
        </tr>
        <tr>
          <td width="180"><?=$LANG["label_re_enter_password"]?></td>
          <td><input type="password" name="password_2" style="width: 100px" value="<?=$translator['password']?>" /></td>
        </tr>
        </table>

        <br />
        <h3>Permissions</h3>

        <table class="info" width="600" cellpadding="1" cellspacing="0">
        <tr>
          <td width="180" valign="top">Use of Translations</td>
          <td>
            <div>
              <input type="radio" name="translation_disclaimer" value="only_for_original_project"
                <?php if ($translator["translation_disclaimer"] == "only_for_original_project") echo "checked" ?> id="p1" />
                <label for="p1">Any translations I provide may <b>only</b> be used in the original project</label>
            </div>
            <div>
              <input type="radio" name="translation_disclaimer" value="use_anywhere"
                <?php if ($translator["translation_disclaimer"] == "use_anywhere") echo "checked" ?> id="p2" />
                <label for="p2">Translations I provide may be used for any purpose</label>
            </div>
          </td>
        </tr>
        </table>

        <p>
          <input type="submit" name="update_translator" value="<?=$LANG['word_update']?>" />
        </p>

      </form>

    </div>

  </div>


  <div id="tab2_content" <?php if ($curr_tab != 2) echo "style=\"display: none;\""; ?>>

    <div style="padding: 5px;">

      <h3><?=$LANG["word_statistics"]?></h3>

      <table class="info" width="100%" cellpadding="2" cellspacing="1">
      <tr>
        <th><?=$LANG["word_translations"]?></th>
        <th><?=$LANG["label_total_translations"]?></th>
        <th><?=$LANG["label_total_reviews"]?></th>
        <th><?=$LANG["word_reliability"]?></th>
        <th><?=$LANG["label_review_points"]?></th>
        <th><?=$LANG["label_translation_points"]?></th>
      </tr>
      <tr>
        <td><?=$LANG["label_all_languages"]?></td>
        <td><?=$translator["total_translations"]?></td>
        <td><?=$translator["total_reviews"]?></td>
        <td><?=$translator["total_percent_reliable"]?></td>
        <td><?=$translator["total_review_points"]?></td>
        <td><?=$translator["total_translation_points"]?></td>
      </tr>
      <?php
      foreach ($translator_stats as $stats)
      {
        $origin_language = ot_get_language_name($stats["origin_language_id"]);
        $target_language = ot_get_language_name($stats["target_language_id"]);

        echo "<tr>
                <td>$origin_language - $target_language</td>
                <td>{$stats['num_translations']}</td>
                <td>{$stats['num_reviews']}</td>
                <td>{$stats['percent_reliability']}</td>
                <td>{$stats['review_points']}</td>
                <td>{$stats['translation_points']}</td>
              </tr>";
      }
      ?>
      </table>

      <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <input type="hidden" name="translator_id" value="<?=$translator_id?>" />

        <p>
          <input type="submit" name="update_statistics" value="<?=$LANG['label_update_statistics']?>" />
        </p>

     </form>

    </div>

  </div>


  <div id="tab3_content" <?php if ($curr_tab != 3) echo "style=\"display: none;\""; ?>>

    <div style="padding: 5px;">

      <form action="<?=$_SERVER['PHP_SELF']?>" method="post" onsubmit="update_selected_projects(this);">
        <input type="hidden" name="translator_id" value="<?=$translator_id?>" />

        <table class="info" width="660" cellpadding="1" cellspacing="0">
        <?php

        // get all ORDERED language pairs that are relevant for this translator. e.g. English -> Spanish
        // and Spanish -> English.
        $language_pairs = ot_get_distinct_ordered_size_2_subsets($translator["language_ids"]);

        foreach ($language_pairs as $pair)
        {
          $origin_language_id = $pair[0];
          $origin_language = ot_get_language_name($origin_language_id);

          $translation_language_id = $pair[1];
          $translation_language = ot_get_language_name($translation_language_id);

          // get all the projects that need translation between these two languages
          $projects = ot_get_all_available_projects($origin_language_id, $translation_language_id);

          // if there are no projects with this combination of languages, don't output the row
          if (empty($projects))
            continue;

          $custom_suffix = "_{$origin_language_id}_{$translation_language_id}";
        ?>
        <tr>
          <td valign="top" width="130" class="blue"><?php echo "$origin_language - $translation_language"; ?></td>
          <td>

            <table cellpadding="0" cellspacing="0" class="list_table">
            <tr height="20">
              <td class="th medium_grey" width="140">&nbsp;<?=$LANG["label_available_projects"]?></td>
              <td class="th" width="120"> </td>
              <td class="th medium_grey" width="205">&nbsp;<?=$LANG["label_selected_projects"]?></td>
            </tr>
            <tr>
              <td width="180">
                <select name="available_projects<?=$custom_suffix?>" id="available_projects<?=$custom_suffix?>" size="5" multiple style="width: 200px;">
                  <?php
                  foreach ($projects as $project_info)
                  {
                    $project_id   = $project_info["project_id"];
                    $project_name = $project_info["name"];

                    // if the translator has signed up to translate the project into this language, don't show it
                    if (isset($translator_projects_languages[$project_id]) && is_array($translator_projects_languages[$project_id]) && in_array($translation_language_id, $translator_projects_languages[$project_id]))
                      continue;

                    echo "<option value='$project_id'>$project_name ($project_id)</option>\n";
                  }
                  ?>
                </select>
              </td>
              <td align="center">
                <input type="button" value="<?=$LANG['word_add_arrow_uc']?>" onclick="moveOptions(this.form.available_projects<?=$custom_suffix?>, this.form['selected_projects<?=$custom_suffix?>[]']);" /><br />
                <br />
                <input type="button" value="<?=$LANG['word_remove_arrow_uc']?>" onclick="moveOptions(this.form['selected_projects<?=$custom_suffix?>[]'], this.form.available_projects<?=$custom_suffix?>);" />
              </td>
              <td width="205">
                <select name="selected_projects<?=$custom_suffix?>[]" size="5" multiple style="width: 200px;">
                  <?php
                  foreach ($translator_projects as $project_info)
                  {
                    // if this project's origin language isn't in this particular origin_language_id, ignore it
                    if ($project_info["origin_language_id"] != $origin_language_id)
                      continue;

                    $project_id = $project_info["project_id"];

                    // if the translators hasn't signed up to translate this project into the current
                    // language, don't show it
                    if (!in_array($translation_language_id, $translator_projects_languages[$project_id]))
                      continue;

                    $project_name = $project_info["name"];
                    echo "<option value='$project_id'>$project_name ($project_id)</option>\n";
                  }
                  ?>
                </select>
              </td>
            </tr>
            </table>

            <script type="text/javascript">
            selected_project_suffixes.push("<?=$custom_suffix?>");
            </script>

          </td>
        </tr>
        <?php
        }
        ?>
        </table>

        <p>
          <input type="submit" name="update_translator_projects" value="<?=$LANG['word_update']?>" />
        </p>

      </form>

    </div>

  </div>

  <div class="hr"></div>

  <p>
    <a href="index.php"><?=$LANG["label_backtotranslators"]?></a>
  </p>


<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
