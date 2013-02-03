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
      foreach ($languages as $language)
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
<!--
<tr>
  <td>Enable Email Notifications</td>
  <td>
    <input type="radio" name="receive_email_notifications" value="yes" id="receive_email_notifications1" <?php if ($account_info["receive_email_notifications"] == "yes") echo "checked"; ?> />
      <label for="receive_email_notifications1"><?=$LANG["word_yes"]?></label>
    <input type="radio" name="receive_email_notifications" value="no" id="receive_email_notifications2" <?php if ($account_info["receive_email_notifications"] == "no") echo "checked"; ?> />
      <label for="receive_email_notifications2"><?=$LANG["word_no"]?></label>
  </td>
</tr>
-->
</table>
