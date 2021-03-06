<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');

require("_my_account.php");
?>
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = $LANG["label_my_account"];
  require("$g_root_dir/global/header_code.php");
  ?>
</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/translators/"),
                  array($LANG["label_my_account"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["label_my_account"]?></h1>
  <br />

  <?=ot_display_message($success, $message)?>

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post" onsubmit="checkSelected(this['selected_languages[]'])">

    <h3><?=$LANG["label_login_info"]?></h3>
    <table class="info" width="600" cellpadding="1" cellspacing="0">
    <tr>
      <td width="180"><?=$LANG["word_email"]?></td>
      <td><input type="text" name="email" style="width: 200px" value="<?=$account_info['email']?>" /></td>
    </tr>
    <tr>
      <td><?=$LANG["word_password"]?></td>
      <td><input type="password" name="password" style="width: 100px" value="<?=$account_info['password']?>" /></td>
    </tr>
    <tr>
      <td><?=$LANG["label_re_enter_password"]?></td>
      <td><input type="password" name="password_2" style="width: 100px" value="<?=$account_info['password']?>" /></td>
    </tr>
    </table>

    <br />
    <h3><?=$LANG["label_account_info"]?></h3>

    <table class="info" width="600" cellpadding="1" cellspacing="0">
    <tr>
      <td width="180"><?=$LANG["label_ui_language"]?></td>
      <td>
        <select name="ui_language_id">
          <option value=""><?=$LANG["label_please_select"]?></option>
          <?php
          foreach ($ui_languages as $language)
          {
            $selected = "";
            if ($language['language_id'] == $account_info['ui_language_id'])
              $selected = "selected";

            echo "<option value='{$language['language_id']}' $selected>{$language['language_name']}</option>\n";
          }
          ?>
        </select>
      </td>
    </tr>
    <tr>
      <td><?=$LANG["label_first_name"]?></td>
      <td><input type="text" name="first_name" value="<?=$account_info['first_name']?>" /></td>
    </tr>
    <tr>
      <td><?=$LANG["label_last_name"]?></td>
      <td><input type="text" name="last_name" value="<?=$account_info['last_name']?>" /></td>
    </tr>
    <tr>
      <td valign="top"><?=$LANG["label_languages_spoken"]?></td>
      <td>

        <table cellpadding="0" cellspacing="0" class="list_table">
        <tr height="20">
          <td class="th medium_grey" width="140">&nbsp;<?=$LANG["label_available_languages"]?></td>
          <td class="th" width="120"> </td>
          <td class="th medium_grey" width="200">&nbsp;<?=$LANG["label_selected_languages"]?></td>
        </tr>
        <tr>
          <td width="135">
            <select name="available_languages" id="available_languages" size="6" multiple style="width: 130px;">
              <?php
              foreach ($languages as $language)
                echo "<option value='{$language['language_id']}'>{$language['language_name']}</option>\n";
              ?>
            </select>
          </td>
          <td align="center">
            <input type="button" value="<?=$LANG['word_add_arrow_uc']?>" onclick="moveOptions(this.form.available_languages, this.form['selected_languages[]']);" /><br />
            <br />
            <input type="button" value="<?=$LANG['word_remove_arrow_uc']?>" onclick="moveOptions(this.form['selected_languages[]'], this.form.available_languages);" />
          </td>
          <td width="135">
            <select name="selected_languages[]" size="6" multiple style="width: 130px;">
              <?php
              foreach ($languages as $language_info)
              {
                if (!in_array($language_info["language_id"], $account_info["language_ids"]))
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
          <option value="5"  <?php if ($account_info["ui_num_data_per_page"] == "5") echo "selected"; ?>>5</option>
          <option value="10" <?php if ($account_info["ui_num_data_per_page"] == "10") echo "selected"; ?>>10</option>
          <option value="15" <?php if ($account_info["ui_num_data_per_page"] == "15") echo "selected"; ?>>15</option>
          <option value="20" <?php if ($account_info["ui_num_data_per_page"] == "20") echo "selected"; ?>>20</option>
          <option value="25" <?php if ($account_info["ui_num_data_per_page"] == "25") echo "selected"; ?>>25</option>
          <option value="30" <?php if ($account_info["ui_num_data_per_page"] == "30") echo "selected"; ?>>30</option>
          <option value="40" <?php if ($account_info["ui_num_data_per_page"] == "40") echo "selected"; ?>>40</option>
          <option value="50" <?php if ($account_info["ui_num_data_per_page"] == "50") echo "selected"; ?>>50</option>
        </select>
      </td>
    </tr>
    <tr>
      <td width="180">Default Bulk Translate View</td>
      <td>
        <select name="default_bulk_translate_view">
          <option value="detailed"  <?php if ($account_info["default_bulk_translate_view"] == "detailed") echo "selected"; ?>>Detailed</option>
          <option value="short" <?php if ($account_info["default_bulk_translate_view"] == "short") echo "selected"; ?>>Short</option>
        </select>
      </td>
    </tr>
    <tr>
      <td>Enable Email Notifications</td>
      <td>
        <input type="radio" name="receive_email_notifications" value="yes" id="receive_email_notifications1" <?php if ($account_info["receive_email_notifications"] == "yes") echo "checked"; ?> />
          <label for="receive_email_notifications1"><?=$LANG["word_yes"]?></label>
        <input type="radio" name="receive_email_notifications" value="no" id="receive_email_notifications2" <?php if ($account_info["receive_email_notifications"] == "no") echo "checked"; ?> />
          <label for="receive_email_notifications2"><?=$LANG["word_no"]?></label>
      </td>
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
            <?php if ($account_info["translation_disclaimer"] == "only_for_original_project") echo "checked" ?> id="p1" />
            <label for="p1">Any translations I provide may <b>only</b> be used in the original project</label>
        </div>
        <div>
          <input type="radio" name="translation_disclaimer" value="use_anywhere"
            <?php if ($account_info["translation_disclaimer"] == "use_anywhere") echo "checked" ?> id="p2" />
            <label for="p2">Translations I provide may be used for any purpose</label>
        </div>

      </td>
    </tr>
    </table>

    <p>
      <input type="submit" name="update_account" value="<?=$LANG['word_update']?>" />
    </p>

  </form>


<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
