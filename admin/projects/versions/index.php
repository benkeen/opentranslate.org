<?php
session_start();
header("Cache-control: private");
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
  var page_ns = {};
  page_ns.delete_version = function(version_id)
  {
    if (confirm("Are you sure you want to delete this version? EVERYTHING relating to the version will be lost!"))
    {
    	window.location = "index.php?delete=" + version_id;
    }
  }
  </script>
</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_projects"], "../"),
                  array($project["name"], "../project.php"),
                  array($LANG["word_versions"], ""),
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["label_project_versions"]?></h1>
  <br />

  <div><?=$LANG["text_project_versions_page_summary"]?></div>
  <br />

  <table class="info" cellspacing="0" cellpadding="1" width="100%">
  <tr>
    <th><?=$LANG['label_version_label']?></th>
    <th><?=$LANG['label_date_created']?></th>
    <th align='center' width="120"><?=$LANG['label_available_to_translate']?></th>
    <th align='center' width="70"><?=$LANG['word_visible']?></th>
    <th align='center' width="70"><?=$LANG['word_edit_uc']?></th>
    <th align='center' width="70"><?=$LANG['word_delete_uc']?></th>
  </tr>

  <?php

  for ($i=0; $i<count($versions); $i++)
  {
    $version_id = $versions[$i]['version_id'];
    $date_created = ot_get_date("", $versions[$i]["date_created"], "M jS, g:i A");
    $available_to_translate = ucwords($versions[$i]["may_translate"]);
    $is_visible = ucwords($versions[$i]["is_visible"]);

 	  // colour code the Visible and Available to Translate columns
    $is_translatable_class = ($available_to_translate == "Yes") ? "green" : "light_grey";
    $is_visible_class = ($is_visible == "Yes") ? "green" : "light_grey";

    echo "
       <tr>
         <td>{$versions[$i]['version_label']}</td>
         <td>$date_created</td>
         <td class='$is_translatable_class'>$available_to_translate</td>
         <td class='$is_visible_class'>$is_visible</td>
         <td align='center'><a href='edit_version.php?version_id={$version_id}'>{$LANG['word_edit_uc']}</a></td>
         <td align='center'><a href='#' onclick='return page_ns.delete_version({$version_id})'>{$LANG['word_delete_uc']}</a></td>
       </tr>";
  }
  ?>
  </table>

  <p>
    <form action="new_version.php" method="post">
      <input type="submit" name="add" value="<?=$LANG['label_new_version']?>" />
    </form>
  </p>

  <br />
  <div class="hr"></div>
<!--
	<h3>Version structure</h3>

  <?php
//  ot_display_version_tree($project_);
  ?>


  <br />
  <div class="hr"></div>
-->

  <p>
    <a href="../project.php"><?=$LANG["label_backtoproject"]?></a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>