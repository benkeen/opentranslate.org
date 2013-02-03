<?php
session_start();
header("Cache-control: private");

require("_edit_version.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = "";
  require("$g_root_dir/global/header_code.php");
  ?>
  <script type="text/javascript">
  /* <![CDATA[ */
  var rules = [];
  rules.push("required,version_label,<?=$LANG['validation_version_label']?>");
  rules.push("required,is_visible,<?=$LANG['validation_version_visible']?>");
  rules.push("required,may_translate,<?=$LANG['validation_version_translatable']?>");
  rules.push("required,data_source,<?=$LANG['validation_specify_data_source']?>");
  rules.push("if:data_source=new,required,data_source_categories,<?=$LANG['validation_version_cats']?>");
  rules.push("if:data_source=old,required,data_source_data,<?=$LANG['validation_version_data']?>");
  /* ]]> */
  </script>

</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_projects"], "$g_root_url/admin/projects/"),
                  array($project["name"], "../project.php"),
                  array($LANG["word_versions"], "index.php"),
                  array($LANG["label_edit_version"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["label_edit_version"]?></h1>

  <br />

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post" onsubmit="checkSelected(this['selected_languages[]']); return validateFields(this, rules)">
    <input type="hidden" name="version_id" value="<?=$version_id?>" />

    <table width="100%" cellpadding="0" cellspacing="1" class="info">
    <tr>
      <td width="12" valign="top" class="red bold">*</td>
      <td valign="top" width="150"><?=$LANG["label_version_label"]?></td>
      <td>
        <div class="medium_grey"><?=$LANG["text_project_version_label"]?></div>
        <input type="text" name="version_label" style="width: 200px;" value="<?php echo htmlspecialchars($version['version_label']); ?>" />
      </td>
    </tr>
    <tr>
      <td valign="top" class="red bold"> </td>
      <td valign="top">Export folder</td>
			<td>
        <div class="medium_grey">
				  If you plan on exporting your project data to another site via FTP, this setting is required. It's the 
					name of the subfolder on your site where this version's data will be uploaded. The parent folder is 
					specified on your main <a href="../settings/">Settings page</a> (the FTP Site Folder setting).
				</div>
        <input type="text" name="export_folder" style="width: 200px;" value="<?php echo htmlspecialchars($version['export_folder']); ?>" />
			</td>
    </tr>
    <tr>
      <td valign="top" class="red bold"> </td>
      <td valign="top"><?=$LANG["label_version_description"]?></td>
      <td><textarea name="synopsis" style="width: 100%; height: 80px;"><?=$version["synopsis"]?></textarea></td>
    </tr>
    <tr>
      <td valign="top" class="red bold">*</td>
      <td valign="top"><?=$LANG["label_is_visible"]?></td>
      <td>
        <div class="medium_grey"><?=$LANG["text_project_version_is_visible"]?></div>
        <input type="radio" name="is_visible" id="is_visible1" value="yes" <?php if ($version["is_visible"] == "yes") echo "checked"; ?> /><label for="is_visible1"><?=$LANG["word_yes"]?></label>
        <input type="radio" name="is_visible" id="is_visible2" value="no" <?php if ($version["is_visible"] == "no") echo "checked"; ?> /><label for="is_visible2"><?=$LANG["word_no"]?></label>
      </td>
    </tr>
    <tr>
      <td valign="top" class="red bold">*</td>
      <td valign="top"><?=$LANG["label_available_to_translate"]?></td>
      <td>
        <div class="medium_grey"><?=$LANG["text_project_version_is_translatable"]?></div>
        <input type="radio" name="may_translate" id="may_translate1" value="yes" <?php if ($version["may_translate"] == "yes") echo "checked"; ?> /><label for="may_translate1"><?=$LANG["word_yes"]?></label>
        <input type="radio" name="may_translate" id="may_translate2" value="no"  <?php if ($version["may_translate"] == "no") echo "checked"; ?> /><label for="may_translate2"><?=$LANG["word_no"]?></label>
      </td>
    </tr>
    <tr>
      <td valign="top" class="red">*</td>
      <td width="160" valign="top"><?=$LANG["label_data_export_options"]?></td>
      <td>
        <div class="medium_grey"><?=$LANG["text_data_export_options"]?></div>

        <table cellspacing="0" cellpadding="0">
        <tr>
          <td width="150">
            <input type="checkbox" name="export_types[]" id="data_export_type1" value="XML"   <?php if (in_array("XML", $export_types)) echo "checked"; ?> /><label for="data_export_type1">XML</label> <span class="green bold">*</span><br/>
            <input type="checkbox" name="export_types[]" id="data_export_type2" value="PHP"   <?php if (in_array("PHP", $export_types)) echo "checked"; ?> /><label for="data_export_type2">PHP</label> <span class="green bold">*</span><br/>
            <input type="checkbox" name="export_types[]" id="data_export_type3" value="Excel" <?php if (in_array("Excel", $export_types)) echo "checked"; ?> /><label for="data_export_type3">Excel</label>
          </td>
          <td valign="top">
            <input type="checkbox" name="export_types[]" id="data_export_type4" value="CSV"   <?php if (in_array("CSV", $export_types)) echo "checked"; ?>/><label for="data_export_type4">CSV</label><br/>
            <input type="checkbox" name="export_types[]" id="data_export_type5" value="text"  <?php if (in_array("text", $export_types)) echo "checked"; ?>/><label for="data_export_type5">Text file</label><br/>
          </td>
        </tr>
        </table>

      </td>
    </tr>
    <tr>
      <td valign="top" class="red">*</td>
      <td width="160" valign="top"><?=$LANG["label_show_labels_on_translator_pages"]?></td>
      <td>
        <div class="medium_grey"><?=$LANG["text_show_labels_option"]?></div>

        <input type="radio" name="show_labels_on_translator_pages" id="show_labels1" value="yes"
				  <?php if ($version["show_labels_on_translator_pages"] == "yes") echo "checked"; ?> /><label for="show_labels1">Yes</label>
        <input type="radio" name="show_labels_on_translator_pages" id="show_labels2" value="no"
				  <?php if ($version["show_labels_on_translator_pages"] == "no") echo "checked"; ?> /><label for="show_labels2">No</label>

      </td>
    </tr>
    </table>

    <p>
      <input type="submit" name="update_version" value="<?=$LANG['word_update']?>" />
    </p>

  </form>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
