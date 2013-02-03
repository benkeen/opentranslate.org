
    <table class="info" cellspacing="0" cellpadding="1">
    <tr>
      <td width="180"><?=$LANG["label_first_name"]?></td>
      <td><input type="text" name="first_name" value="<?php echo htmlspecialchars($account_info['first_name']); ?>" maxlength="50" /></td>
    </tr>
    <tr>
      <td><?=$LANG["label_last_name"]?></td>
      <td><input type="text" name="last_name" value="<?php echo htmlspecialchars($account_info['last_name']); ?>" maxlength="50" /></td>
    </tr>
    <tr>
      <td><?=$LANG["word_email"]?></td>
      <td><input type="text" name="email" value="<?=$account_info['email']?>" style="width: 200px;" maxlength="100" /></td>
    </tr>
    <tr>
      <td><?=$LANG["word_password"]?></td>
      <td><input type="password" name="password" value="<?=$account_info['password']?>" style="width: 80px;" maxlength="100" /></td>
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
    <tr>
      <td class="no_underline">Enable Email Notifications</td>
      <td class="no_underline">
        <input type="radio" name="receive_email_notifications" value="yes" id="receive_email_notifications1" <?php if ($account_info["receive_email_notifications"] == "yes") echo "checked"; ?> />
          <label for="receive_email_notifications1"><?=$LANG["word_yes"]?></label>
        <input type="radio" name="receive_email_notifications" value="no" id="receive_email_notifications2" <?php if ($account_info["receive_email_notifications"] == "no") echo "checked"; ?> />
          <label for="receive_email_notifications2"><?=$LANG["word_no"]?></label>
      </td>
    </tr>
    </table>

    <br />
    <h3><?=$LANG["label_permissions"]?></h3>

    <table class="info" cellspacing="0" cellpadding="1">
    <tr>
      <td width="230"><?=$LANG["label_can_create_projects"]?></td>
      <td>
        <input type="radio" name="can_create_projects" id="can_create_projects1" value="yes" <?php if ($account_info["can_create_projects"] == "yes") echo "checked"; ?> />
          <label for="can_create_projects1"><?=$LANG["word_yes"]?></label>
        <input type="radio" name="can_create_projects" id="can_create_projects2" value="no" <?php if ($account_info["can_create_projects"] == "no") echo "checked"; ?> />
          <label for="can_create_projects2"><?=$LANG["word_no"]?></label>
      </td>
    </tr>
    <tr>
      <td><?=$LANG["label_can_create_project_manager_accounts"]?></td>
      <td>
        <input type="radio" name="can_create_project_manager_accounts" id="can_create_project_manager_accounts1" value="yes" <?php if ($account_info["can_create_project_manager_accounts"] == "yes") echo "checked"; ?> />
          <label for="can_create_project_manager_accounts1"><?=$LANG["word_yes"]?></label>
        <input type="radio" name="can_create_project_manager_accounts" id="can_create_project_manager_accounts2" value="no" <?php if ($account_info["can_create_project_manager_accounts"] == "no") echo "checked"; ?> />
          <label for="can_create_project_manager_accounts2"><?=$LANG["word_no"]?></label>
      </td>
    </tr>
    <tr>
      <td><?=$LANG["label_can_create_translator_accounts"]?></td>
      <td>
        <input type="radio" name="can_create_translator_accounts" id="can_create_translator_accounts1" value="yes" <?php if ($account_info["can_create_translator_accounts"] == "yes") echo "checked"; ?> />
          <label for="can_create_translator_accounts1"><?=$LANG["word_yes"]?></label>
        <input type="radio" name="can_create_translator_accounts" id="can_create_translator_accounts2" value="no" <?php if ($account_info["can_create_translator_accounts"] == "no") echo "checked"; ?> />
          <label for="can_create_translator_accounts2"><?=$LANG["word_no"]?></label>
      </td>
    </tr>
    <tr>
      <td class="no_underline"><?=$LANG["label_can_export_data"]?></td>
      <td class="no_underline">
        <input type="radio" name="can_export_data" id="can_export_data1" value="yes" <?php if ($account_info["can_export_data"] == "yes") echo "checked"; ?> />
          <label for="can_export_data1"><?=$LANG["word_yes"]?></label>
        <input type="radio" name="can_export_data" id="can_export_data2" value="no" <?php if ($account_info["can_export_data"] == "no") echo "checked"; ?> />
          <label for="can_export_data2"><?=$LANG["word_no"]?></label>
      </td>
    </tr>
    </table>
