<?php
session_start();
header("Cache-control: private");
require("_php_file_import_review.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DT XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = "";
  require("$g_root_dir/global/header_code.php");
  ?>

<script type="text/javascript">
function sortable_lists_onload()
{
  <?php
  $sortable_list_ids = array("\"data_list\"");
  foreach ($category_info as $cat)
  {
    $category_id   = $cat["category_id"];
    $sortable_list_ids[] = "\"sortable_list_cat_$category_id\"";
  }
  $containment = join(", ", $sortable_list_ids);

  foreach ($sortable_list_ids as $sortable_list_id)
  {
    echo "Sortable.create($sortable_list_id, { tag:'div', containment: [$containment], dropOnEmpty:true, only:'list_item' });\n";
  }
//  echo "Sortable.create($sortable_list_id, { tag:'div', containment: [$containment], dropOnEmpty:true, only:'list_item' });\n";

  ?>
}
</script>

<style type="text/css">
.sortable_list {
  list-style: none;
  margin: 0px;
  padding-left: 0px;
}
div.list_item {
  background-color: #ffffff;
  margin:1px;
  height:22px;
  padding-left: 2px;
  position: relative;
  cursor: move;
}
div.category_group {
  border: 1px solid #bbbbbb;
  background-color: #efefef;
  padding: 2px;
  margin-bottom: 5px;
}
div.category_group_heading {
  height: 20px;
  border-bottom: 1px solid #B8E0FF;
  padding-left: 2px;
  background-color: #D2EBFF;
}
</style>

</head>
<body onload="sortable_lists_onload()">

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_projects"], "$g_root_url/admin/projects/"),
                  array($project["name"], "../project.php"),
                  array($LANG["label_import_data"], "index.php"),
                  array($LANG["label_review_import_data"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["label_review_import_data"]?></h1>

  <p><?=$LANG["text_php_import_review_page_summary"]?></p>

  <?=ot_display_message($success, $message)?>

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post" enctype="multipart/form-data">

    <table cellspacing="1" cellpadding="2" class="info" width="100%">
    <tr>
      <th width="50%">Available Categories</th>
      <th width="50%">Data Needing Categorizing</th>
    </tr>
    <tr>
      <td valign="top">

        <?php
        foreach ($category_info as $cat)
        {
          $category_id   = $cat["category_id"];
          $category_name = $cat["category_name"];

          echo "
          <div class='category_group' id='sortable_list_cat_$category_id'>
            <div class='category_group_heading'>Category: <b>$category_name</b></div>
          </div>";
        }
        ?>
      </td>
      <td valign="top">

        <div class="category_group"id='data_list'>
        <?php
        $row = 1;
        foreach ($php_vars[0] as $key=>$value)
        {
          $key   = htmlspecialchars($key);
          $value = htmlspecialchars($value);
          echo "<div class='list_item'>
                  <table cellspacing='0' cellpaddding='0' width='100%'>
                  <tr>
                    <td width='10%'> </td>
                    <td width='45%'><input type=\"text\" name=\"row_{$row}_label\" value=\"$key\" style=\"width: 100%\" /></td>
                    <td width='45%' class='value_str'><input type=\"text\" name=\"row_{$row}_value\" value=\"$value\" style=\"width: 100%\" /></td>
                  </tr>
                  </table>
                </div>";

          $row++;
        }
        ?>
        </div>

      </td>
    </tr>
    </table>

  </form>

  <br />
  <div class="hr"></div>

  <p>
    <a href="../project.php">&lt;&lt; <?=$LANG["label_backtoproject"]?></a>
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
