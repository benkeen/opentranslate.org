<?php
session_start();
header("Cache-control: private");
require("_auto_translate_language.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = "";
  require("$g_root_dir/global/header_code.php");
  ?>

  <script>
  var page_ns = {};
  page_ns.num_translations_per_chunk = 10;
	page_ns.started = false;

  // this function is called iteratively and translates
  page_ns.translate = function()
  {
	  if (!page_ns.started)
		{
		  $("responses").show();
		  $("responses").innerHTML += "Starting translating, please wait...<br />";
			page_ns.started = true;
		}

		var url = "<?=$g_root_url?>/global/code/actions.php";
		var params = {
      version_id: <?=$version_id?>,
      language_id: <?=$language_id?>,
      action: "auto_translate",
			num_items: $("num_items").value
		};
		new Ajax.Request(url, {
		  method: "post",
		  parameters: params,
		  onSuccess: page_ns.translateSuccess
		});
  }

  page_ns.translateSuccess = function(transport)
  {
    var info = transport.responseText.evalJSON();

    if (info.success)
		{
		  $("responses").innerHTML += "Translated " + info.num_translated_items + " items, " 
			  + info.num_remaining_items + " remaining.<br />";

		 if (parseInt(info.num_remaining_items) == 0)
		 {
		   $("responses").innerHTML += "Translation complete.<br />";
		 }
		 else
		 {
		   page_ns.translate();
		 }
		}
		else
		{
      $("responses").innerHTML += "There was a problem with the auto translation: " + info.error + "<br />";		  
		}
  }
  </script>
</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_projects"], "$g_root_url/admin/projects/"),
                  array($project["name"], "../project.php"),
                  array("Auto-translate", "auto_translate.php"),
                  array("Translate Language", ""),
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <?php require("../../../global/change_project_version_form.php"); ?>

  <h1 class="margin_bottom_large">Auto-translate</h1>

  <table cellspacing="0" cellpadding="1" width="100%">
	<tr>
	  <td>Num items per request</td>
		<td>
		  <select id="num_items">
			  <option value="10" selected>10</option>
			  <option value="20">20</option>
			  <option value="50">50</option>
			  <option value="100">100</option>
      </select>								
		</td>
	</tr>
	<?php
  while ($version_lang = mysql_fetch_assoc($statistics_query))
  {
    $curr_language_id = $version_lang["language_id"];

    if ($language_id != $curr_language_id)
      continue;

  	$language    = $version_lang["language_name"];
    $percent_reliability = $version_lang["percent_reliability"];
    $percent_translated  = $version_lang["percent_translated"];

    echo <<<EOF
      <tr>
        <td width="140">Language</td>
        <td>$language</td>
      </tr>
      <tr>
        <td>Percent translated</td>
        <td>$percent_translated%</td>
      </tr>
EOF;
  }
  ?>
  </table>

  <p>
    <input type="submit" value="Refresh Stats" onclick="window.location='<?php echo $_SERVER["PHP_SELF"]?>?refresh_stats=1'"/>
    <input type="submit" value="Auto-translate &raquo;" onclick="page_ns.translate()" />
  </p>

  <div id="responses" class="box1" style="display:none">

  </div>

  <div class="hr"></div>

  <p>
    <a href="../project.php"><?=$LANG["label_backtoproject"]?></a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>