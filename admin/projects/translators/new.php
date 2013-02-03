<?php
session_start();
header("Cache-control: private");
require("_new.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = $LANG["label_add_translator"];
  require("$g_root_dir/global/header_code.php");
  ?>

  <script type="text/javascript">
  /* <![CDATA[ */
  var generated_password = "<?=ot_generate_password()?>";
  /* ]]> */
  </script>

</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_translators"], "index.php"),
                  array($project["name"], "../project.php"),
                  array($LANG["label_add_translator"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1>Add Translator</h1>

  <p>
    This page lets you create a new translator account which will be automatically assigned to
    this project.
  </p>

  <?= ot_display_message($success, $message); ?>

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post" onsubmit="checkSelected(this['selected_languages[]']);">
    <input type="hidden" name="project_id" value="<?=$project_id?>" />

    <h3>Account Information</h3>

    <table class="info" width="600" cellpadding="1" cellspacing="0">
    <tr>
      <td width="180"><?=$LANG["word_status"]?></td>
      <td>
        <input type="radio" name="status" value="active" id="status1"   <?php if ($page["status"] == "active") echo "checked"; ?> /><label for="status1" class="active"><?=$LANG["word_active"]?></label>
        <input type="radio" name="status" value="disabled" id="status2" <?php if ($page["status"] == "disabled") echo "checked"; ?> /><label for="status2" class="disabled"><?=$LANG["word_disabled"]?></label>
        <input type="radio" name="status" value="pending" id="status3"  <?php if ($page["status"] == "pending") echo "checked"; ?>/><label for="status3" class="pending"><?=$LANG["word_pending"]?></label>
        <input type="radio" name="status" value="blacklisted" id="status4"  <?php if ($page["status"] == "blacklisted") echo "checked"; ?>/><label for="status4"><?=$LANG["word_blacklisted"]?></label>
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
            if ($language["ui_version_available"] == "no")
              continue;

            $selected = "";
            if ($language['language_id'] == $page['ui_language_id'])
              $selected = "selected";

            echo "<option value='{$language['language_id']}' $selected>{$language['language_name']}</option>\n";
  			  }
          ?>
        </select>
      </td>
    </tr>
    <tr>
      <td><?=$LANG["label_first_name"]?></td>
      <td><input type="text" name="first_name" value="<?=$page['first_name']?>" /></td>
    </tr>
    <tr>
      <td><?=$LANG["label_last_name"]?></td>
      <td><input type="text" name="last_name" value="<?=$page['last_name']?>" /></td>
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
            <input type="button" value="ADD ->" onclick="moveOptions(this.form.available_languages, this.form['selected_languages[]']);" /><br />
            <br />
            <input type="button" value="<- REMOVE" onclick="moveOptions(this.form['selected_languages[]'], this.form.available_languages);" />
          </td>
          <td width="135">
            <select name="selected_languages[]" size="6" multiple style="width: 130px;">
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
          <option value="5">5</option>
          <option value="10" selected>10</option>
          <option value="15">15</option>
          <option value="20">20</option>
          <option value="25">25</option>
          <option value="30">30</option>
          <option value="40">40</option>
          <option value="50">50</option>
        </select>
      </td>
    </tr>
    <tr>
      <td width="180">Default Bulk Translate View</td>
      <td>
        <select name="default_bulk_translate_view">
          <option value="detailed">Detailed</option>
          <option value="short" selected>Short</option>
        </select>
      </td>
    </tr>
    </table>


    <br />
    <h3>Login Information</h3>

    <table class="info" width="600" cellpadding="1" cellspacing="0">
    <tr>
      <td width="180"><?=$LANG["word_email"]?></td>
      <td><input type="text" name="email" style="width: 200px" value="<?=$page['email']?>" /></td>
    </tr>
    <tr>
      <td width="180"><?=$LANG["word_password"]?></td>
      <td>
        <input type="password" name="password" style="width: 100px" value="<?=$page['password']?>" />
        <input type="button" value="<?=$LANG['word_generate']?>" onclick="this.form.password.value=generated_password; this.form.password_2.value=generated_password;"/>
      </td>
    </tr>
    <tr>
      <td width="180"><?=$LANG["label_re_enter_password"]?></td>
      <td><input type="password" name="password_2" style="width: 100px" value="<?=$page['password']?>" /></td>
    </tr>
    </table>


    <p>
      <input type="submit" name="add_translator" value="<?=$LANG['label_add_translator']?>" />
    </p>

  </form>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
