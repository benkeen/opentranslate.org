<?php
session_start();
header("Cache-control: private");
require("_auto_translate.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = "";
  require("$g_root_dir/global/header_code.php");
  ?>
</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_projects"], "$g_root_url/admin/projects/"),
                  array($project["name"], "../project.php"),
                  array("Auto-translate", "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <?php require("../../../global/change_project_version_form.php"); ?>

  <h1>Auto-translate</h1>

  <p>
    This page lets you automatically translate any untranslated content using the
    <a href="http://code.google.com/apis/ajaxlanguage/" target="_blank">Google Translate API</a>. Auto-translated text is associated with a special "Google Translate"
    translator account. <a href="auto_translate_all.php">Click here</a> to auto translate all languages in the page.
  </p>

  <table cellspacing="0" cellpadding="0" width="100%" class="info margin_top_large">
	<tr>
	  <th>Language</th>
		<th width="140">% Translated</th>
		<th width="140">% Unreviewed<br/>auto-translations</th>
		<th width="140">% Reliable</th>
  	<th width="110"> </th>
  </tr>
  <?php
  while ($version_lang = mysql_fetch_assoc($statistics_query))
  {
    $language    = $version_lang["language_name"];
    $language_id = $version_lang["language_id"];
    $percent_reliability = $version_lang["percent_reliability"];
    $percent_translated  = $version_lang["percent_translated"];
		$percent_unreviewed = ot_get_percentage_unreviewed_auto_translations($version_id, $language_id);

    if (empty($version_lang["google_translate_code"]))
    {
      $auto_translate_str = "<span class=\"light_grey\">Unavailable</span>";
    }
    else
    {
      $auto_translate_disabled = ($percent_translated == 100) ? "disabled" : "";
    	$auto_translate_str = "<input type=\"button\" value=\"Auto-translate\" $auto_translate_disabled onclick=\"window.location='auto_translate_language.php?language_id=$language_id'\" />";
    }

    if ($origin_language_id == $version_lang["language_id"])
      continue;

    echo <<<EOF
         <tr>
            <td class="bold">$language</td>
            <td>
              <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td width="35" class="no_underline">$percent_translated%</td>
                <td class="no_underline">
                  <div style="height: 6px; width:100%; border: 1px solid #cccccc;">
                    <div class="statistics_percent_translated" style="width: $percent_translated%"> </div>
                  </div>
                </td>
              </tr>
              </table>
            </td>
            <td>
              <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td width="35" class="no_underline">&nbsp;$percent_unreviewed%</td>
                <td class="no_underline">
                  <div style="height: 6px; width:100%; border: 1px solid #cccccc;">
                    <div class="statistics_percent_unreviewed_autotranslated" style="width: $percent_unreviewed%;"> </div>
                  </div>
                </td>
              </tr>
              </table>
            </td>
            <td>
              <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td width="35" class="no_underline">&nbsp;$percent_reliability%</td>
                <td class="no_underline">
                  <div style="height: 6px; width:100%; border: 1px solid #cccccc;">
                    <div class="statistics_percent_reliable" style="width: $percent_reliability%;"> </div>
                  </div>
                </td>
              </tr>
              </table>
            </td>
            <td align="center">$auto_translate_str</td>
          </tr>
EOF;

  }
  ?>
  </table>

  <div class="hr"></div>

  <p>
    <a href="../project.php"><?=$LANG["label_backtoproject"]?></a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
