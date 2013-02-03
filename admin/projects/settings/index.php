<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');

require("_index.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = "";
  require("$g_root_dir/global/header_code.php");
  ?>

  <script type="text/javascript">
  function toggle_ftp_fields()
  {
    var is_disabled = true;
    if (document.settings_form.enable_ftp[0].checked)
      is_disabled = false;

    $("ftp_hostname").disabled = is_disabled;
    $("ftp_site_folder").disabled = is_disabled;
    $("ftp_username").disabled = is_disabled;
    $("ftp_password").disabled = is_disabled;
    $("test_ftp_settings").disabled = is_disabled;

    if (is_disabled)
      $("test_ftp_settings").style.color = '#999999';
    else
      $("test_ftp_settings").style.color = '#333333';
  }
  </script>

</head>
<body onload="toggle_ftp_fields()">

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_projects"], "$g_root_url/admin/projects/"),
                  array($project["name"], "../project.php"),
                  array($LANG["word_settings"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["word_settings"]?></h1>

  <br/>

  <?=ot_display_message($success, $message)?>

  <div><?=$LANG["text_settings_page_summary"]?></div>
  <br/>

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="settings_form">

    <table class="info" width="100%" cellpadding="1" cellspacing="0">
    <tr>
      <td width="20" valign="top" class="red">*</td>
      <td width="200"><?=$LANG["label_origin_language"]?></td>
      <td class="bold"><?=$origin_language_name?></td>
    </tr>
    <tr>
      <td valign="top" class="red">*</td>
      <td><?=$LANG["word_status"]?></td>
      <td>
        <input type="radio" name="status" value="online" id="status3" <?php if ($project["status"] == "online") echo "checked"; ?> /><label for="status3" class="green"><?=$LANG["word_online"]?></label>
        <input type="radio" name="status" value="new" id="status1" <?php if ($project["status"] == "new") echo "checked"; ?> /><label for="status1" class="pending"><?=$LANG["label_new_incomplete"]?></label>
        <input type="radio" name="status" value="offline" id="status2" <?php if ($project["status"] == "offline") echo "checked"; ?> /><label for="status2" class="red"><?=$LANG["word_offline"]?></label>
        <input type="radio" name="status" value="archived" id="status4" <?php if ($project["status"] == "archived") echo "checked"; ?> /><label for="status4" class="medium_grey"><?=$LANG["word_archived"]?></label>
      </td>
    </tr>
    <tr>
      <td valign="top" class="red">*</td>
      <td><?=$LANG["label_project_visibility"]?></td>
      <td>
        <select name="project_visibility">
          <option value="public"  <?php if ($project["project_visibility"] == "public") echo "selected"; ?>><?=$LANG["word_public"]?></option>
          <option value="private" <?php if ($project["project_visibility"] == "private") echo "selected"; ?>><?=$LANG["word_private"]?></option>
        </select>
      </td>
    </tr>
    <tr>
      <td valign="top" class="red">*</td>
      <td width="180"><?=$LANG["label_trust_threshold"]?></td>
      <td>
        <select name="trust_threshold">
          <option value=""><?=$LANG["label_not_applicable"]?></option>
          <option value="1" <?php if ($project["trust_threshold"] == "1") echo "selected"; ?>>1</option>
          <option value="2" <?php if ($project["trust_threshold"] == "2") echo "selected"; ?>>2</option>
          <option value="3" <?php if ($project["trust_threshold"] == "3") echo "selected"; ?>>3</option>
          <option value="4" <?php if ($project["trust_threshold"] == "4") echo "selected"; ?>>4</option>
          <option value="5" <?php if ($project["trust_threshold"] == "5") echo "selected"; ?>>5</option>
          <option value="6" <?php if ($project["trust_threshold"] == "6") echo "selected"; ?>>6</option>
          <option value="7" <?php if ($project["trust_threshold"] == "7") echo "selected"; ?>>7</option>
          <option value="8" <?php if ($project["trust_threshold"] == "8") echo "selected"; ?>>8</option>
          <option value="9" <?php if ($project["trust_threshold"] == "9") echo "selected"; ?>>9</option>
          <option value="10" <?php if ($project["trust_threshold"] == "10") echo "selected"; ?>>10</option>
        </select>
      </td>
    </tr>
    <tr>
      <td valign="top" class="red">*</td>
      <td><?=$LANG["label_translator_blacklist_threshold"]?></td>
      <td>
        <select name="translator_blacklist_threshold">
          <option value=""><?=$LANG["label_not_applicable"]?></option>
          <option value="1" <?php if ($project["translator_blacklist_threshold"] == "1") echo "selected"; ?>>1</option>
          <option value="2" <?php if ($project["translator_blacklist_threshold"] == "2") echo "selected"; ?>>2</option>
          <option value="3" <?php if ($project["translator_blacklist_threshold"] == "3") echo "selected"; ?>>3</option>
          <option value="4" <?php if ($project["translator_blacklist_threshold"] == "4") echo "selected"; ?>>4</option>
          <option value="5" <?php if ($project["translator_blacklist_threshold"] == "5") echo "selected"; ?>>5</option>
          <option value="6" <?php if ($project["translator_blacklist_threshold"] == "6") echo "selected"; ?>>6</option>
          <option value="7" <?php if ($project["translator_blacklist_threshold"] == "7") echo "selected"; ?>>7</option>
          <option value="8" <?php if ($project["translator_blacklist_threshold"] == "8") echo "selected"; ?>>8</option>
          <option value="9" <?php if ($project["translator_blacklist_threshold"] == "9") echo "selected"; ?>>9</option>
          <option value="10" <?php if ($project["translator_blacklist_threshold"] == "10") echo "selected"; ?>>10</option>
        </select>
      </td>
    </tr>
    </table>

    <p class="bold"><?=$LANG["label_ftp_settings"]?></p>

    <table class="info" width="100%" cellpadding="1" cellspacing="0">
    <tr>
      <td width="20" valign="top" class="red">*</td>
      <td width="200"><?=$LANG["label_enable_ftp"]?></td>
      <td>
        <input type="radio" name="enable_ftp" value="yes" id="eftp1" <?php if ($project["enable_ftp"] == "yes") echo "checked"; ?>
          onclick="toggle_ftp_fields()" onblur="toggle_ftp_fields()" /><label for="eftp1"><?=$LANG["word_yes"]?></label>
        <input type="radio" name="enable_ftp" value="no" id="eftp2" <?php if ($project["enable_ftp"] == "no") echo "checked"; ?>
          onclick="toggle_ftp_fields()" onblur="toggle_ftp_fields()" /><label for="eftp2"><?=$LANG["word_no"]?></label>
      </td>
    </tr>
    <tr>
      <td valign="top" class="red">*</td>
      <td><?=$LANG["label_ftp_hostname"]?></td>
      <td><input type="text" name="ftp_hostname" id="ftp_hostname" value="<?=$project['ftp_hostname']?>" style="width:100%" maxlength="250" /></td>
    </tr>
    <tr>
      <td valign="top" class="red">*</td>
      <td><?=$LANG["label_ftp_site_folder"]?></td>
      <td><input type="text" name="ftp_site_folder" id="ftp_site_folder" value="<?=$project['ftp_site_folder']?>" style="width:100%" maxlength="250" /></td>
    </tr>
    <tr>
      <td valign="top" class="red">*</td>
      <td><?=$LANG["label_ftp_username"]?></td>
      <td><input type="text" name="ftp_username" id="ftp_username" value="<?=$project['ftp_username']?>" style="width:120px" maxlength="100" /></td>
    </tr>
    <tr>
      <td valign="top" class="red">*</td>
      <td><?=$LANG["label_ftp_password"]?></td>
      <td><input type="password" name="ftp_password" id="ftp_password" value="<?=$project['ftp_password']?>" style="width:120px" maxlength="100" /></td>
    </tr>
    </table>

    <p>
      <input type="submit" name="update_settings" value="<?=$LANG['word_update']?>" />
      <input type="submit" name="test_ftp_settings" id="test_ftp_settings" value="<?=$LANG['label_test_ftp_settings']?>" />
    </p>

  </form>

  <br />
  <div class="hr"></div>

  <p>
    <a href="../project.php"><?=$LANG["label_backtoproject"]?></a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
