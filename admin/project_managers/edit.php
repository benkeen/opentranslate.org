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
  $g_page_title = $LANG["label_edit_project_manager"];
  require("$g_root_dir/global/header_code.php");
  ?>

  <script type="text/javascript">
  /* <![CDATA[ */
  var rules = [];
  rules.push("required,first_name,<?=$LANG['message_no_first_name']?>")
  rules.push("required,last_name,<?=$LANG['message_no_last_name']?>")
  rules.push("required,email,<?=$LANG['message_no_email']?>")
  rules.push("valid_email,email,<?=$LANG['message_invalid_email']?>")
  rules.push("required,password,<?=$LANG['message_no_password']?>")
  rules.push("required,ui_language_id,<?=$LANG['message_no_ui_language_id']?>")
  /* ]]> */
  </script>

</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["label_project_managers"], "$g_root_url/admin/project_managers/"),
                  array($LANG["label_edit_project_manager"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["label_edit_project_manager"]?></h1>

  <p><?=$LANG["text_edit_project_manager_page_summary"]?></p>

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post" onsubmit="checkSelected(this['selected_projects[]']); return validateFields(this, rules)">
    <input type="hidden" name="account_id" value="<?=$account_id?>" />

    <table>
    <tr>
      <td width="130"><?=$LANG["label_first_name"]?></td>
      <td><input type="text" name="first_name" value="<?php echo htmlspecialchars($page['first_name']); ?>" maxlength="50" /></td>
    </tr>
    <tr>
      <td><?=$LANG["label_last_name"]?></td>
      <td><input type="text" name="last_name" value="<?php echo htmlspecialchars($page['last_name']); ?>" maxlength="50" /></td>
    </tr>
    <tr>
      <td><?=$LANG["word_email"]?></td>
      <td><input type="text" name="email" value="<?=$page['email']?>" style="width: 200px;" maxlength="100" /></td>
    </tr>
    <tr>
      <td><?=$LANG["word_password"]?></td>
      <td>
        <input type="password" name="password" value="<?=$page['password']?>" style="width: 80px;"
          maxlength="100" /><input type="button" value="<?=$LANG['word_generate']?>" onclick="this.form.password.value='<?=ot_generate_password()?>'"/>
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
            if ($language['language_id'] == 25)
              $selected = "selected";

            echo "<option value='{$language['language_id']}' $selected>{$language['language_name']}</option>\n";
  			  }
          ?>
        </select>
      </td>
    </tr>
    </table>

		<br />
    <h3><?=$LANG["word_projects"]?></h3>

    <table cellpadding="0" cellspacing="0" class="list_table">
    <tr height="20">
      <td class="th medium_grey" width="200"><?=$LANG["label_available_projects"]?></td>
      <td class="th" width="120"> </td>
      <td class="th medium_grey" width="200"><?=$LANG["label_selected_projects"]?></td>
    </tr>
    <tr>
      <td width="135">
        <select name="available_projects" id="available_projects" size="6" multiple style="width: 200px;">
          <?php
    			foreach ($projects as $project)
    			{
    			  $project_id   = $project["project_id"];
    			  $project_name = $project["name"];

            if (!in_array($project_id, $selected_project_ids))
    			    echo "<option value='$project_id'>$project_name</option>\n";
    			}
          ?>
        </select>
      </td>
      <td align="center">
        <input type="button" value="<?=$LANG['word_add_arrow_uc']?>" onclick="moveOptions(this.form.available_projects, this.form['selected_projects[]']);" /><br />
        <br />
        <input type="button" value="<?=$LANG['word_remove_arrow_uc']?>" onclick="moveOptions(this.form['selected_projects[]'], this.form.available_projects);" />
      </td>
      <td width="135">
        <select name="selected_projects[]" size="6" multiple style="width: 200px;">
          <?php
          foreach ($page["projects"] as $project)
    			{
    			  $project_id   = $project["project_id"];
    			  $project_name = $project["name"];
    			  echo "<option value='$project_id'>$project_name</option>\n";
    			}
          ?>
        </select>
      </td>
    </tr>
    </table>

    <br />
    <h3><?=$LANG["label_permissions"]?></h3>

    <table>
    <tr>
      <td width="230"><?=$LANG["label_can_create_projects"]?></td>
      <td>
        <input type="radio" name="can_create_projects" id="can_create_projects1" value="yes" <?php if ($page["can_create_projects"] == "yes") echo "checked"; ?> />
          <label for="can_create_projects1"><?=$LANG["word_yes"]?></label>
        <input type="radio" name="can_create_projects" id="can_create_projects2" value="no" <?php if ($page["can_create_projects"] == "no") echo "checked"; ?> />
          <label for="can_create_projects2"><?=$LANG["word_no"]?></label>
      </td>
    </tr>
    <tr>
      <td><?=$LANG["label_can_create_project_manager_accounts"]?></td>
      <td>
        <input type="radio" name="can_create_project_manager_accounts" id="can_create_project_manager_accounts1" value="yes" <?php if ($page["can_create_project_manager_accounts"] == "yes") echo "checked"; ?> />
          <label for="can_create_project_manager_accounts1"><?=$LANG["word_yes"]?></label>
        <input type="radio" name="can_create_project_manager_accounts" id="can_create_project_manager_accounts2" value="no" <?php if ($page["can_create_project_manager_accounts"] == "no") echo "checked"; ?> />
          <label for="can_create_project_manager_accounts2"><?=$LANG["word_no"]?></label>
      </td>
    </tr>
    <tr>
      <td><?=$LANG["label_can_create_translator_accounts"]?></td>
      <td>
        <input type="radio" name="can_create_translator_accounts" id="can_create_translator_accounts1" value="yes" <?php if ($page["can_create_translator_accounts"] == "yes") echo "checked"; ?> />
          <label for="can_create_translator_accounts1"><?=$LANG["word_yes"]?></label>
        <input type="radio" name="can_create_translator_accounts" id="can_create_translator_accounts2" value="no" <?php if ($page["can_create_translator_accounts"] == "no") echo "checked"; ?> />
          <label for="can_create_translator_accounts2"><?=$LANG["word_no"]?></label>
      </td>
    </tr>
    <tr>
      <td><?=$LANG["label_can_export_data"]?></td>
      <td>
        <input type="radio" name="can_export_data" id="can_export_data1" value="yes" <?php if ($page["can_export_data"] == "yes") echo "checked"; ?> />
          <label for="can_export_data1"><?=$LANG["word_yes"]?></label>
        <input type="radio" name="can_export_data" id="can_export_data2" value="no" <?php if ($page["can_export_data"] == "no") echo "checked"; ?> />
          <label for="can_export_data2"><?=$LANG["word_no"]?></label>
      </td>
    </tr>
    </table>

    <p>
      <input type="submit" name="add_project_manager" value="<?=$LANG['word_update']?>" />
    </p>

  </form>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
