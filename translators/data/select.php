<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');

require("_select.php");
?>
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = $LANG["word_administration"];
  require("$g_root_dir/global/header_code.php");
  ?>
</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($project["name"], "../project.php"),
                  array($LANG["label_select_translation"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1 class="margin_bottom_large"><?=$LANG["label_select_translation"]?></h1>

  <div>
    Please select the version and the language you would like to translate. Open Translate
    shares all common data between versions to minimize the amount of translation needed.
    In other words, any translations you make in one version will be added to all other
    versions that use the same text.
  </div>
  <br />

  <table class="info" cellspacing="0" cellpadding="1" width="600px">
  <tr>
    <th><?=$LANG['word_version']?></th>
    <th><?=$LANG['label_date_created']?></th>
    <th align="center">Translations Needed</th>
  </tr>

  <?php
  $origin_language_name = ot_get_language_name($project["origin_language_id"]);
  foreach ($versions as $version)
  {
    $version_id = $version['version_id'];
    $date_created = ot_get_date("", $version["date_created"], "M jS Y, g:i A");

		if ($version["is_visible"] != "yes" || $version["may_translate"] != "yes")
		  continue;
		
    $language_links = array();
    foreach ($project_languages as $language_info)
    {
    	$target_language_id = $language_info["language_id"];
    	if (in_array($target_language_id, $translator_info["language_ids"]))
    	{
    		$new_language_name = ot_get_language_name($language_info["language_id"]);
    	  $language_links[] = "<a href=\"{$_SERVER['PHP_SELF']}?version_id=$version_id&language_id=$target_language_id\">$origin_language_name &nbsp;&raquo;&nbsp; $new_language_name</a>";
    	}
    }

    $language_link_str = join("<br />", $language_links);

    echo "
       <tr>
         <td valign='top'>{$version['version_label']}</td>
         <td valign='top'>$date_created</td>
         <td nowrap>$language_link_str</td>
       </tr>";
  }
  ?>
  </table>


<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>