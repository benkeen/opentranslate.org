<?php
session_start();
header("Cache-control: private");
require("_edit_export_only_data.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="<?=$LANG['direction']?>">
<head>

  <script type="text/javascript" src="/global/tiny_mce/tiny_mce.js"></script>
  <script type="text/javascript">
    tinyMCE.init({
      mode : "<?=$tiny_mce_mode?>",
      elements : "data",
      theme : "advanced",
      theme_advanced_toolbar_location : "top",
      theme_advanced_buttons1 : "bold,italic,underline,separator,justifyleft,justifycenter,justifyright,separator,bullist,numlist,separator,outdent,indent,separator,forecolor,backcolor,separator,link,unlink,hr,separator,code",
      theme_advanced_buttons2 : "",
      theme_advanced_buttons3 : "",
      theme_advanced_toolbar_align : "left",
      theme_advanced_path_location : "bottom",
      theme_advanced_resize_horizontal : false,
      theme_advanced_resizing : true,
      height: "120",
      content_css : "/global/tinymce.css"
    });

  function toggle_editor(action)
  {
    //if (tinyMCE.getInstanceById("data") == null)
    if (action == "on")
      tinyMCE.execCommand('mceAddControl', false, "data");
    else
      tinyMCE.execCommand('mceRemoveControl', false, "data");
  }

  function delete_data(data_id)
  {
    var answer = confirm("<?=$LANG['validation_confirm_delete_data']?>");
    if (answer)
    {
      window.location = "<?=$_SERVER['PHP_SELF']?>?delete=" + data_id;
    }
  }
  </script>

  <?php
  $g_page_title = $LANG["word_administration"];
  require("$g_root_dir/global/header_code.php");
  ?>
  <script type="text/javascript" src="/global/general.js"></script>
</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_projects"], "$g_root_url/admin/projects/"),
                  array($project["name"], "../project.php"),
                  array($LANG["word_data"], "./"),
                  array($LANG["label_manage_data"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <?php
  $affected_version_ids = ot_get_data_usage_versions($version_id, $data_id);

  if (!empty($affected_version_ids))
  {
  ?>
	  <div style="float: right;">
	    <form action="<?php echo $_SERVER["PHP_SELF"]?>" method="post">
	      <input type="hidden" name="change_project_version" value="1" />
	      <input type="hidden" name="data_id" value="<?php echo $data_id; ?>" />
	  		<select name="version_id">
	  		<?php
	  		foreach ($affected_version_ids as $curr_version_id)
	  		{
	  			$version_name = ot_get_version_name($curr_version_id);
	  			$selected     = ($curr_version_id == $version_id) ? "selected" : "";
          echo "<option value=\"$curr_version_id\" $selected>$version_name</option>\n";
	  		}
	  		?>
	      </select><input type="submit" value="Select" />
	    </form>
	  </div>
  <?php
  }
  ?>

  <h1><?=$LANG["label_manage_data"]?></h1>

  <p>
    This data item is in an <b>export only</b> category and is therefore not visible to translators.
    You may customize the contents of the value for each language file below.
  </p>

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
    <input type="hidden" name="data_id" value="<?=$request['data_id']?>" />
    <input type="hidden" name="comments_for_translators" value="" />

    <table cellspacing="2" cellpadding="1" width="100%" class="info">
    <tr>
      <td valign="top" class="red">*</td>
      <td width="150"><?=$LANG["label_php_label"]?></td>
      <td><input type="text" name="data_label" value="<?=htmlspecialchars($data['data_label'])?>" style="width:100%;" maxlength="255" /></td>
    </tr>
    <tr>
      <td width="20" class="red">*</td>
      <td><?=$LANG['label_data_type']?></td>
      <td>
        <input type="radio" name="use_html_editor" id="use_html_editor1" value="yes" onclick="toggle_editor('on')"
          <?php if ($data["use_html_editor"] == "yes") echo "checked"; ?> /><label for="use_html_editor1"><?=$LANG["word_html_uc"]?></label>
        <input type="radio" name="use_html_editor" id="use_html_editor2" value="no" onclick="toggle_editor('off')"
          <?php if ($data["use_html_editor"] == "no") echo "checked"; ?> /><label for="use_html_editor2"><?=$LANG["word_text"]?></label>
      </td>
    </tr>
    <tr>
      <td valign="top" class="red">*</td>
      <td valign="top"><?=$LANG["word_category"]?></td>
      <td>
        <select name="category_id">
          <?php
          foreach ($categories as $category)
          {
            $category_id   = $category["category_id"];
            $category_name = $category["category_name"];
            $selected = ($data['category_id'] == $category_id) ? "selected" : "";

            echo "<option value='$category_id'{$selected}>$category_name</option>\n";
          }
          ?>
        </select>
      </td>
    </tr>
    <tr>
      <td valign="top" class="red">*</td>
      <td valign="top"><?=$LANG["label_text_to_translate"]?></td>
      <td><textarea name="data" style="width: 100%; height: 50px"><?=$data["data"]?></textarea></td>
    </tr>
    </table>

    <p class="heading_2">Translations / Custom Values</p>

    <table cellspacing="1" cellpadding="1" width="100%" class="info">
    <tr>
      <th class="pad_right"><?=$LANG["word_language"]?></th>
      <th width="70%">Translation / Custom Language File Value</th>
    </tr>
    <?php
    foreach ($project["languages"] as $lang_info)
    {
      $display_count = 0;
      $translation_str = "";
      $translation_id = "";
      $language_id = $lang_info["language_id"];

      if ($origin_language_id == $language_id)
        continue;

      foreach ($translations as $translation)
      {
        if ($language_id != $translation["language_id"])
          continue;

        $translation_id  = $translation["translation_id"];
        $translation_str = mb_substr($translation["translation"], 0, 50);
      }

      if (!empty($translation_id))
        $name = "special_update_translation_$translation_id";
      else
        $name = "special_add_language_$language_id";

      echo "<tr>
        <td>{$lang_info['language_name']}</td>
        <td nowrap>
          <input type='text' name='$name' value=\"$translation_str\" style='width:100%;'/>
        </td>
      </tr>
      ";
    }
    ?>
    </table>

      <?php
      if (count($affected_version_ids) == 1)
      {
      ?>
        <input type="hidden" name="affected_version_id" value="<?=$version_id?>" />
        <input type="hidden" id="affected_versions" value="single" />

        <p>
	        <input type="submit" name="update_data" value="<?=$LANG['word_update']?>" />
	        <input type="button" class="burgundy" value="<?=$LANG['word_delete']?>" onclick="delete_data(<?=$data['data_id']?>)" />
	      </p>

      <?php
      }
      else
      {
      ?>

			<input type="hidden" id="affected_versions" value="multiple" />

			<div>
				<p>
				  This data is shared by multiple versions. Please indicate which versions should be affected by this update / deletion.
				  Any time you delete or update a version, the change will apply to all children.
				</p>

		  	<h4>Version Tree</h4>
		    <div id="data_version_tree">
			    <?php
            ot_display_data_usage_version_tree($version_id, $data_id, true);
			    ?>
		    </div>

        <?php
				// if this data was deleted from any children, display them here
				$versions = ot_get_versions_that_deleted_data($data_id);
				if (!empty($versions))
				{
				?>

        <div>This data was deleted from the following child version(s):</div>

        <ul>
        <?php
          foreach ($versions as $vid)
          {
          	echo "<li>" . ot_get_version_name($vid) . "</li>\n";
          }
				}
        ?>
        </ul>

		  </td>
        <input type="submit" name="update_data" value="<?=$LANG['word_update']?>" onclick="" />
        &nbsp;
        <input type="button" class="burgundy" value="<?=$LANG['word_delete']?>" onclick="delete_data(<?=$data['data_id']?>)" />

			</div>

      <?php
      }
      ?>
			
		<!--		
    <p>
      <input type="submit" name="update_data" value="<?=$LANG['word_update']?>" />
      <input type="button" class="burgundy" value="<?=$LANG['word_delete']?>" onclick="delete_data(<?=$data['data_id']?>)" />
    </p>
    -->

  </form>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>