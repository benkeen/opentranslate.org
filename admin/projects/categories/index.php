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
</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_projects"], "../"),
                  array($project["name"], "../project.php"),
                  array($LANG["word_categories"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <?php require("../../../global/change_project_version_form.php"); ?>

  <h1><?=$LANG["word_categories"]?>: <?=$project["name"]?></h1>

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
    <input type="hidden" name="reorder_categories" value="1" />

    <?php
    if (empty($categories))
    {
       echo "<p>{$LANG['text_project_no_cats']}</p>";
      ?>

      <p>
        <input type="text" name="new_category" style="width: 120px;" /><input type="submit" name="add" value="<?=$LANG['label_add_category']?>" />
      </p>

      <?php
    }
    else
    {
      if (!$is_child_version)
        echo "<p>{$LANG["text_categories"]}</p>";
      else
      {
    	?>

      <p>
        This version inherits its content from <a href="index.php?version_id=<?=$base_version_id?>">this version</a>. In order to
        edit the category information you will need to edit the base version.
      </p>

      <?php
      }
      ?>
    <table class="info" cellspacing="0" cellpadding="1" width="100%">
    <tr>
      <th height="20" width="40"><?=$LANG["word_order"]?></th>
      <th><?=$LANG["label_category_name"]?></th>
      <th width="120" align="center"><?=$LANG["label_num_items"]?></th>
      <th width="140" align="center"><?=$LANG["label_export_data_only"]?></th>
      <th width="70" align="center"><?=$LANG["word_delete_uc"]?></th>
    </tr>

    <?php
    $current_parent_category = 0;
    $disabled_str = ($is_child_version) ? "disabled" : "";

    for ($i=0; $i<count($categories); $i++)
    {
      $category_id    = $categories[$i]['category_id'];
      $category_name  = $categories[$i]['category_name'];
      $category_order = $categories[$i]['category_order'];
      $export_only    = ($categories[$i]['export_only'] == "yes") ? "checked" : "";

      if (!$is_child_version)
        $num_items_link = "0 <a href='../data/add_data.php?category_id=$category_id&version_id=$version_id'>({$LANG['word_add']})</a>";
      else
        $num_items_link = "0";

      $cat_data = ot_get_category_data($category_id, $version_id);

      if (!empty($cat_data))
      {
        $num_items = count($cat_data);
        $num_items_link = "<a href='../data/index.php?page=1&version_id=$version_id&category_id=$category_id'><b>$num_items</b></a>";
      }

      $delete_link = "<a href='?delete={$category_id}'>{$LANG['word_delete_uc']}</a>";
      if ($is_child_version)
        $delete_link = "<span class=\"light_grey\">{$LANG['word_delete_uc']}</span>";

      echo "
        <tr>
          <td align='center'><input type='text' name='category_{$category_id}_order' value='$category_order' style='width: 30px;' $disabled_str /></td>
          <td><input type='text' name='category_{$category_id}_name' value='$category_name' style='width: 100%;' $disabled_str /></td>
          <td align='center'>$num_items_link</td>
          <td align='center'><input type='checkbox' name='export_only_{$category_id}' value='yes' $export_only $disabled_str /></td>
          <td align='center'>$delete_link</td>
        </tr>";
    }
    ?>
    </table>

    <br />

    <table cellspacing="0" cellpadding="0" width="100%">
    <tr>
      <td><input type="submit" name="update" value="<?=$LANG['word_update']?>" /></td>
      <td align="right">
        <input type="text" name="new_category" style="width: 120px;" /><input type="submit" name="add" value="<?=$LANG['label_add_category']?>" />
      </td>
    </tr>
    </table>

    <?php } ?>

  </form>

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
