<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');

require("_index.php");
?>
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = "";
  require("$g_root_dir/global/header_code.php");
  ?>
  <script>
  var rules = [];
  rules.push("required,selected_languages[],Please select at least one language for this project.");
  </script>
</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_projects"], "$g_root_url/admin/projects/"),
                  array($project["name"], "../project.php"),
                  array("Languages", "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1>Languages</h1>

  <br />
  <?=ot_display_message($success, $message)?>

  <div>
    This page lets you specify which languages are available for translation. These apply to all versions
    of this project.
  </div>

  <br />

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post" onsubmit="checkSelected(this['selected_languages[]']); return validateFields(this, rules)">

    <table width="100%" cellpadding="0" cellspacing="1" class="info">
    <tr>
      <td valign="top" class="red">*</td>
      <td valign="top"><?=$LANG["label_required_translations"]?></td>
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
              {
                // obviously, don't show the ORIGIN language
                if ($project["origin_language_id"] == $language["language_id"])
                  continue;

								// don't show any languages that are already taken
								$language_taken = false;
                foreach ($project["languages"] as $language_info)
                {
								  if ($language_info["language_id"] == $language["language_id"])
									{
									  $language_taken = true;
										break;
									}
								}
								if ($language_taken)
								  continue;

                echo "<option value='{$language['language_id']}'>{$language['language_name']}</option>\n";
              }
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
              <?php
              foreach ($project["languages"] as $language_info)
              {
              	$language_id   = $language_info["language_id"];
              	$language_name = $language_info["language_name"];

                if ($language_id == $origin_language_id)
                  continue;

                echo "<option value='$language_id'>$language_name</option>\n";
              }
              ?>
            </select>
          </td>
        </tr>
        </table>

      </td>
    </tr>
    </table>

    <p>
      <input type="submit" name="update" value="Update" />
    </p>
  </form>


  <div class="hr"></div>

  <p>
    <a href="../project.php">&lt;&lt; Back to Project</a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
