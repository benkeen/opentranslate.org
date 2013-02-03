<?php
session_start();
header("Cache-control: private");

require("_new_version.php");
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
                  array($LANG["label_new_version"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["label_new_version"]?>: <?=$project["name"];?></h1>

	<br />

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post" onsubmit="return validateFields(this, rules)">

    <table width="100%" cellpadding="0" cellspacing="1">
    <tr>
		  <td width="12" valign="top" class="red bold">*</td>
      <td valign="top" width="150"><?=$LANG["label_version_label"]?></td>
    	<td>
  			<div class="medium_grey"><?=$LANG["text_project_version_label"]?></div>
	  		<input type="text" name="version_label" style="width: 200px;"/>
		  	<br/>
		  	<br/>
			</td>
    </tr>
    <tr>
		  <td valign="top" class="red bold"> </td>
      <td valign="top"><?=$LANG["label_version_description"]?></td>
    	<td>
			  <textarea name="synopsis" style="width: 100%; height: 80px;"></textarea>
				<br/>
			</td>
    </tr>
    <tr>
		  <td valign="top" class="red bold">*</td>
      <td valign="top"><?=$LANG["label_data_source"]?></td>
    	<td>
				<div class="medium_grey"><?=$LANG["text_label_data_source_text"]?></div>

					<input type="checkbox" name="data_source" id="data_source" value="yes" />
					  <label for="data_source"><?=$LANG["text_new_version_old_data"]?></label>
	  				<select name="parent_version_id">
	    			  <?php
	            foreach ($versions as $version)
	            {
	              $version_id    = $version["version_id"];
	              $version_label = $version["version_label"];
	              echo "<option value='$version_id'>$version_label</option>\n";
	            }
	    				?>
	  				</select>
        <br/>
        <br/>
      </td>
    </tr>
    <tr>
		  <td valign="top" class="red bold">*</td>
      <td valign="top"><?=$LANG["label_is_visible"]?></td>
    	<td>
				<div class="medium_grey"><?=$LANG["text_project_version_is_visible"]?></div>
    	  <input type="radio" name="is_visible" id="is_visible1" value="yes" /><label for="is_visible1"><?=$LANG["word_yes"]?></label>
    	  <input type="radio" name="is_visible" id="is_visible2" value="no" checked /><label for="is_visible2"><?=$LANG["word_no"]?></label>
				<br/>
				<br/>
      </td>
    </tr>
    <tr>
		  <td valign="top" class="red bold">*</td>
      <td valign="top"><?=$LANG["label_available_to_translate"]?></td>
    	<td>
				<div class="medium_grey"><?=$LANG["text_project_version_is_translatable"]?></div>
    	  <input type="radio" name="may_translate" id="may_translate1" value="yes" checked /><label for="may_translate1"><?=$LANG["word_yes"]?></label>
    	  <input type="radio" name="may_translate" id="may_translate2" value="no" /><label for="may_translate2"><?=$LANG["word_no"]?></label>
				<br/>
				<br/>
      </td>
    </tr>
    </table>

    <p>
      <input type="submit" name="add_version" value="<?=$LANG['word_add']?>" />
    </p>

  </form>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
