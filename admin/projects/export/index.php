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
  function submit_ftp_form()
  {
    var fields = $$(".ftp_export_data");

    if (!fields.length)
    {
      alert("Please check those rows that you would like to export via FTP.");
      return false;
    }

    var info = new Array();
    for (var i=0; i<fields.length; i++)
    {
      if (fields[i].checked)
        info.push(fields[i].value);
    }
    $("ftp_export_content").value = info.join(",");
  }

  function deselect_all_export()
  {
    var fields = $$(".ftp_export_data");
    for (var i=0; i<fields.length; i++)
      fields[i].checked = false;
  }
  </script>

</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_projects"], "$g_root_url/admin/projects/"),
                  array($project["name"], "../project.php"),
                  array($LANG["label_export_data"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <?php require("../../../global/change_project_version_form.php"); ?>

  <h1 class="margin_bottom_large"><?=$LANG["label_export_data"]?></h1>

  <?=ot_display_message($success, $message)?>

  <div>
    This page lets you export via FTP the PHP language files for each language and each version in this 
		project. Incomplete translations will be filled in with content from the original language.
  </div>
	
  <br />

  <?php
  if ($project["ftp_settings_confirmed"] != "yes")
  {
  ?>
    <div class="notify"><span><span><span><span><span><span><span><span>
		  Your FTP settings haven't been configured yet. Please check the <a href="../settings">Settings</a> page.
		</span></span></span></span></span></span></span></span></div>
    <br />
  <?php
  }
  ?>
		
	<table cellspacing="0" cellpadding="1">
	<tr>
	  <td valign="top" width="220"><a href="project_summary_file.php">View the project's summary file</a></td>
		<td>
		  A unique <b>project_summary.php</b> file is created and exported to your export root folder (specified 
			on your main <a href="../settings/">Settings</a> page under the "FTP Site Folder" setting. This file contains 
			the top level project statistics and information about the project versions.   
		</td>
	</tr>
	<tr>
	  <td valign="top"><a href="version_export_settings.php?version_id=<?php echo $version_id?>">Update version's export settings</a></td>
		<td>
		  This controls the content to be inserted at the top of the export files. 
		</td>
	</tr>
	<tr>
	  <td valign="top"><a href="version_summary_file.php?version_id=<?php echo $version_id?>">View version's summary file</a></td>
		<td>
		  The language files for each project version are exported to a subfolder, whose name is defined on the 
			<a href="../versions/edit_version.php?version_id=<?php echo $version_id?>">Edit Version</a> page. 
			In addition to the language files, a <b>summary.php</b> file is created in 
			that folder that contains the various stats and information about this version.
		</td>
	</tr>
	</table>

	<br />
		
  <table cellspacing="1" cellpadding="1" width="100%" class="info">
  <tr>
    <td width="25"><input type="button" value="Unselect" onclick="deselect_all_export()" /></td>
    <th><?=$LANG['word_language']?></th>
    <th width="40%"><?=$LANG['label_percent_translated']?></th>
    <th width="80" align="center" nowrap><?=$LANG['word_settings']?></th>
    <th width="80" align="center" nowrap><?=$LANG['word_status']?></th>
    <th width="20" align="center" nowrap><?=$LANG['word_view']?></th>
  </tr>
  <tr height="30" class="highlight">
    <td align="center">
      <input type="checkbox" class="ftp_export_data" id="f<?=$origin_language_id?>" value="<?=$origin_language_id?>"
      <?php
      $checked = "";
      if (!isset($request["ftp_export_content"]))
        $checked = "checked";
      else if (is_array($request["ftp_export_content"]) && in_array($origin_language_id, $request["ftp_export_content"]))
        $checked = "checked";
      echo $checked;
      ?>
      />
    </td>
    <td width="20%" class="bold blue pad_right" nowrap><label for="f<?=$origin_language_id?>"><?=$origin_language?> <?=$LANG["word_original_p"]?></label></td>
    <td>
      <table cellspacing='0' cellpadding='0' width='100%'>
      <tr>
        <td class='no_underline' width='50'>(100%)</td>
        <td class='no_underline'>
          <div style='height: px; width:100%; border: 1px solid #cccccc;'>
            <div class='statistics_percent_translated' style='width: 100%;'> </div>
          </div>
        </td>
      </tr>
      </table>
    </td>
    <td align="center"><a href="php_export_language_file_settings.php?language_id=<?=$origin_language_id?>"><?=$LANG["word_settings"]?></a></td>
    <td align="center">
      <?php
      $origin_lang_settings = ot_get_version_language_info($version_id, $origin_language_id);
      $php_export_status = $origin_lang_settings["php_export_status"];
      $status_str   = "";
      $disabled_str = "";
      switch ($php_export_status)
      {
        case "Complete":
          $status_str = "<span class='green'>Available</span>";
          break;
        case "Incomplete":
          $status_str = "<span class='red'>Incomplete</span>";
          $disabled_str = "disabled style='color:#999999;' ";
          break;
      }
      echo $status_str;
      ?>
    </td>
    <td>
      <form action="view_translation_file_contents.php" method="post">
        <input type='hidden' name='language_id' value='<?=$origin_language_id?>' />
        <input type='submit' name='view' value="<?=$LANG['word_view']?>" <?=$disabled_str?> />
      </form>
    </td>
  </tr>
  <?php
  while ($version_lang = mysql_fetch_assoc($statistics_query))
  {
    $language            = $version_lang["language_name"];
    $language_id         = $version_lang["language_id"];
    $percent_reliability = $version_lang["percent_reliability"];
    $percent_translated  = $version_lang["percent_translated"];
    $php_export_status   = $version_lang["php_export_status"];

    if ($language_id == $origin_language_id)
      continue;

    $status_str   = "";
    $disabled_str = "";
    switch ($php_export_status)
    {
      case "Complete":
        $status_str = "<span class='green'>Available</span>";
        break;
      case "Incomplete":
        $status_str = "<span class='red'>Incomplete</span>";
        $disabled_str = "disabled style='color:#999999;' ";
        break;
    }

    echo "<tr height='30'>
            <td align='center'><input type='checkbox' class='ftp_export_data' id='f$language_id' value='$language_id' checked /></td>
            <td class='bold pad_right'><label for='f$language_id'>$language</label></td>
            <td>
              <table cellspacing='0' cellpadding='0' width='100%'>
              <tr>
                <td class='no_underline' width='50'>$percent_translated%</td>
                <td class='no_underline'>
                  <div style='height: 6px; width:100%; border: 1px solid #cccccc;'>
                    <div class='statistics_percent_translated' style='width: $percent_translated%;'> </div>
                  </div>
                </td>
              </tr>
              </table>
            </td>
            <td align='center'><a href='php_export_language_file_settings.php?language_id=$language_id'>{$LANG["word_settings"]}</a></td>
            <td align='center'>$status_str</td>
            <td width='40'>
              <form action='view_translation_file_contents.php' method='post'>
                <input type='hidden' name='language_id' value='$language_id' />
                <input type='submit' name='view' value=\"{$LANG['word_view']}\" $disabled_str />
              </form>
            </td>
          </tr>";
  }
  ?>
  <tr height="30">
    <td align="center"><input type="checkbox" class="ftp_export_data" id="f_info" value="summary" checked /></td>
    <td colspan="4"><label for="f_info">Project version summary / information file</label></td>
    <td>
      <form action='view_translation_summary_file.php' method="post">
        <input type='submit' name='view' value="<?=$LANG['word_view']?>"  />
      </form>
    </td>
  </tr>
  </table>

  <?php
  if ($project["ftp_settings_confirmed"] == "yes")
  {
  ?>
	  <br />
		
    <form action="<?=$_SERVER['PHP_SELF']?>" method="post" onsubmit="submit_ftp_form()">
		  <div>
			  <input type="checkbox" value="yes" id="overwrite" name="overwrite" /> 
				  <label for="overwrite">Overwrite all exported files</label>
			</div>

      <input type="hidden" name="ftp_export_content" id="ftp_export_content" value="" />
      <p>
        <input type="submit" name="ftp_export" value="Transfer checked rows via FTP (current version only)" /><br />
        <input type="submit" name="ftp_export_all_versions" value="Transfer checked rows via FTP (all versions)" />
      </p>
    </form>
  <?php
  }
  ?>

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
