<?php
session_start();
header("Cache-control: private");
require("_php_file_import_review.php");
?>
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = "";
  require("$g_root_dir/global/header_code.php");
  ?>

  <script type="text/javascript">
  function bulk_set_cats()
  {
    var from = parseInt($("bulk_set_cat_from_row").value);
    var to   = parseInt($("bulk_set_cat_to_row").value);
    var category_id = $("bulk_set_cat_category_id").value;

    for (i=from; i<=to; i++)
    {
      if (document.review_form["row_" + i + "_category_id"] != undefined)
        document.review_form["row_" + i + "_category_id"].value = category_id;
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
                  array($LANG["label_import_data"], "index.php"),
                  array($LANG["label_review_import_data"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["label_review_import_data"]?></h1>

  <p><?=$LANG["text_php_import_review_page_summary"]?></p>

  <?=ot_display_message($success, $message)?>

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="review_form">

		<?php
		$category_options = "";
		foreach ($category_info as $cat)
		{
			$category_id   = $cat["category_id"];
			$category_name = $cat["category_name"];

			$category_options .= "<option value='$category_id'>$category_name</option>\n";
		}

		$set_categories_txt = $LANG["label_set_categories"];
		$set_categories_txt = preg_replace("/%%x%%/", "<input type='text' size='3' id='bulk_set_cat_from_row' value='1' />", $set_categories_txt);
		$set_categories_txt = preg_replace("/%%y%%/", "<input type='text' size='3' id='bulk_set_cat_to_row' value='1' />", $set_categories_txt);
		?>

    <p>
      <?php echo $set_categories_txt; ?>
      <select id="bulk_set_cat_category_id">
        <?=$category_options?>
      </select>
      <input type="button" value="<?=$LANG['word_submit']?>" onclick="bulk_set_cats()" />
    </p>

    <table cellspacing="1" cellpadding="1" width="100%" class="info">
    <tr>
      <th><?=$LANG["word_row"]?></th>
      <th><?=$LANG["label_php_label"]?></th>
      <th>Text to translate</th>
      <th><?=$LANG["word_category"]?></th>
    </tr>
		<?php
		$row = 1;
		foreach ($php_vars[0] as $key=>$value)
		{
			$key   = htmlspecialchars($key);
			$value = htmlspecialchars($value);
			$cat_string = "<select name='row_{$row}_category_id'>$category_options</select>";
			echo "<tr>
			        <td class='bold'>$row</td>
							<td width='45%'><input type=\"text\" name=\"row_{$row}_label\" value=\"$key\" style=\"width: 100%\" /></td>
							<td width='45%' class='value_str'><input type=\"text\" name=\"row_{$row}_value\" value=\"$value\" style=\"width: 100%\" /></td>
							<td width='10%'>$cat_string</td>
						</tr>";

			$row++;
		}
		$num_rows = $row - 1;
		?>
		</table>

    <input type="hidden" name="num_rows" value="<?=$num_rows?>" />

    <p>
      <input type="submit" name="import_data" value="<?=$LANG['label_import_data']?>" />
    </p>

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
