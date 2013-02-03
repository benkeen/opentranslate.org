<?php
session_start();
header("Cache-control: private");

require("_statistics.php");
?>
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
                  array($LANG["word_dashboard"], "$g_root_url/translators/"),
                  array($project["name"], "project.php"),
                  array($LANG["word_statistics"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <?php require("../global/change_project_version_form.php"); ?>

  <h1><?=$LANG["word_statistics"]?></h1>

  <p>
    This page show the statistics for all languages of this project. The statistics are updated every hour. You can view the 
		statistics for that particular version by selecting it in the dropdown to the top right.
  </p>

  <table cellspacing="0" cellpadding="0" width="100%" class="info">
	<tr>
	  <th>Language</th>
		<th width="180">% Translated</th>
		<th width="180">% Unreviewed auto-translations</th>
		<th width="180">% Reliable</th>
	</tr>
  <?php
  while ($version_lang = mysql_fetch_assoc($statistics_query))
  {
    $language = $version_lang["language_name"];
    $percent_reliability = $version_lang["percent_reliability"];
    $percent_translated  = $version_lang["percent_translated"];
		$percent_unreviewed = ot_get_percentage_unreviewed_auto_translations($version_id, $version_lang["language_id"]);

    if ($version_lang["language_id"] == $project["origin_language_id"])
      continue;

    // only show those languages > 0% translated
    //if ($percent_translated <= 0)
    //  continue;
		
    echo "<tr>
            <td valign='top' class='bold'>$language</td>
            <td>
              <table width='100%' cellpadding='0'>
              <tr>
                <td width='43' class='no_underline'>&nbsp;$percent_translated%</td>
                <td class='no_underline'>
                  <div style='height: 8px; width:100%; border: 1px solid #cccccc;'>
                    <div class='statistics_percent_translated' style='width: $percent_translated%;'> </div>
                  </div>
                </td>
							</tr>
							</table>
						</td>
            <td>
              <table width='100%' cellpadding='0'>
              <tr>
                <td width='43' class='no_underline'>&nbsp;$percent_unreviewed%</td>
                <td class='no_underline'>
                  <div style='height: 8px; width:100%; border: 1px solid #cccccc;'>
                    <div class='statistics_percent_unreviewed_autotranslated' style='width: $percent_unreviewed%;'> </div>
                  </div>
                </td>
							</tr>
							</table>
						</td>
						<td>
              <table width='100%' cellpadding='0'>
              <tr>
                <td width='43' class='no_underline'>&nbsp;$percent_reliability%</td>
                <td class='no_underline'>
                  <div style='height: 8px; width:100%; border: 1px solid #cccccc;'>
                    <div class='statistics_percent_reliable' style='width: $percent_reliability%;'>&nbsp;</div>
                  </div>
                </td>
              </tr>
              </table>
            </td>
          </tr>\n";
  }
  ?>
  </table>

  <br />

  <div class="hr"></div>

  <p>
    <a href="project.php"><?=$LANG["label_backtoproject"]?></a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
