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
</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_projects"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["word_projects"]?></h1>

  <p><?=$LANG["text_projects_page_summary"]?></p>

  <table class="info" width="100%" cellpadding="1" cellspacing="0">
  <tr>
    <th width="30" align="center"><?=$LANG["word_id_uc"]?></th>
    <th><?=$LANG["label_project_name"]?></th>
    <th width="100" align="center"><?=$LANG["word_status"]?></th>
    <th width="150" align="center"><?=$LANG["label_last_modified"]?></th>
	  <th width="70" align="center"><?=$LANG["word_select_uc"]?></th>
	  <th width="70" align="center"><?=$LANG["word_delete_uc"]?></th>
  </tr>

  <?php
  foreach ($projects as $project)
  {
    $project_id = $project["project_id"];
    $status_str = "";
    switch ($project["status"])
    {
      case "online":
        $status_str = "<span class='green'>Online</span>";
        break;
      case "offline":
        $status_str = "<span class='red'>Offline</span>";
        break;
      case "new":
        $status_str = "<span class='orange'>New</span>";
        break;
      case "archived":
        $status_str = "<span class='medium_grey'>Archived</span>";
        break;
    }

    // format dates
  	$last_modified = ot_get_date("", $project["last_modified"], "M jS Y, g:i A");

    echo "<tr>
            <td align='center' class='blue bold'>$project_id</td>
            <td>$project[name]</td>
            <td>$status_str</td>
            <td>$last_modified</td>
					  <td align='center'><a href='project.php?project_id={$project["project_id"]}'>{$LANG["word_select_uc"]}</a></td>
					  <td align='center'><a href='index.php?delete={$project["project_id"]}'>{$LANG["word_delete_uc"]}</a></td>
          </tr>";
  }
  ?>
  </table>

  <p>
    <form action="new.php" method="post">
      <input type="submit" value="<?=$LANG['label_new_project']?>" />
  	</form>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>