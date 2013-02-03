<?php
session_start();
header("Cache-control: private");
require("_index.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = $LANG["label_project_managers"];
  require("$g_root_dir/global/header_code.php");
  ?>

  <script type="text/javascript">
  /* <![CDATA[ */
  function delete_project_manager(account_id)
  {
    var answer = confirm("<?=$LANG['validation_delete_project_manager']?>");

    if (answer)
      window.location = "index.php?delete=" + account_id;
  }
  /* ]]> */
  </script>

</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "./"),
                  array($LANG["label_project_managers"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["label_project_managers"]?></h1>

  <p><?=$LANG["text_project_managers_page_summary"]?></p>

  <table class="info" width="100%" cellpadding="1" cellspacing="0">
  <tr>
    <th width="30" align="center"><?=$LANG["word_id_uc"]?></th>
    <th align="center"><?=$LANG["label_project_manager"]?></th>
    <th align="center"><?=$LANG["word_projects"]?></th>
    <th width="130" align="center"><?=$LANG["label_last_logged_in"]?></th>
    <th width="70" align="center"><?=$LANG["word_edit_uc"]?></th>
    <th width="70" align="center"><?=$LANG["word_delete_uc"]?></th>
  </tr>

  <?php
  foreach ($project_managers as $project_manager)
  {
    $account_id = $project_manager["account_id"];
    $first_name = $project_manager["first_name"];
    $last_name  = $project_manager["last_name"];

    // format dates
    if ($project_manager["last_logged_in"] == "0000-00-00 00:00:00")
      $last_logged_in = "<span class='light_grey'>" . $LANG["label_new_account"] . "</span>";
    else
      $last_logged_in = ot_get_date("", $project_manager["last_logged_in"], "M jS, g:i A");

    $projects_html = "";
    if (empty($project_manager["projects"]))
      $projects_html = "<span class='light_grey'>{$LANG['word_unassigned']}</span>";
    else
    {
      $project_links = array();
      foreach ($project_manager["projects"] as $project)
        $project_links[] = "<a href='../projects/project.php?project_id={$project['project_id']}'>{$project['name']}</option>";

      $projects_html = join("<br/>", $project_links);
    }

    echo "<tr>
            <td valign='top' align='center' class='blue bold'>$account_id</td>
            <td valign='top'>$first_name $last_name</td>
            <td valign='top'>$projects_html</td>
            <td valign='top'>$last_logged_in</td>
            <td valign='top'align='center'><a href='edit.php?account_id=$account_id'>{$LANG['word_edit_uc']}</a></td>
            <td valign='top'align='center'><a href='#' onclick='delete_project_manager($account_id)'>{$LANG['word_delete_uc']}</a></td>
          </tr>";
  }
  ?>
  </table>

  <p>
    <form action="new.php" method="post">
      <input type="submit" value="<?=$LANG['label_new_project_manager']?>" />
    </form>
  </p>


<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
