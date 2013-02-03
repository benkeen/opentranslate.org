<?php
session_start();
header("Cache-control: private");
require("_index.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = $LANG["word_administration"];
  require("$g_root_dir/global/header_code.php");
  ?>
	<script type="text/javascript" src="../../../global/general.js"></script>
	<script type="text/javascript" src="../../../global/manage_lists.js"></script>

  <script type="text/javascript">
  /* <![CDATA[ */
  var selected_row_color   = "#ffffcc";
  var unselected_row_color = "#ffffff";
  var current_tab = <?=$curr_tab?>;

  <?php
  $submission_ids = array();
  $data_ids = array();
  while ($data_row = mysql_fetch_assoc($search_query))
    $data_ids[] = $data_row['curr_data_id'];

  if (mysql_num_rows($search_query) > 0)
    mysql_data_seek($search_query, 0);

  // convert the PHP array to JS
  echo "var data_id_str = '" . join(",", $data_ids) . "';\n";
  ?>
  var data_arr = data_id_str.split(",");
  var select_all_ids_returned = <?php if (isset($_SESSION["ot"]["version_{$version_id}_select_all_data_ids"]) && $_SESSION["ot"]["version_{$version_id}_select_all_data_ids"] == "1") echo "true"; else echo "false"; ?>;
  var select_all_ids_on_page = false;
  var num_results = <?=$num_results;?>;
  var version_id = <?=$version_id?>;
  /* ]]> */
  </script>

  <style type="text/css">
  #data_tab1 {
    height:26px;
    text-align: center;
    background-image: url(<?php if ($curr_tab == 1) echo '../../../images/tab_selected.jpg'; else echo '../../../images/tab_unselected.jpg'; ?>);
  }
  #data_tab2 {
    height: 26px;
    text-align: center;
    background-image: url(<?php if ($curr_tab == 2) echo '../../../images/tab_selected.jpg'; else echo '../../../images/tab_unselected.jpg'; ?>);
  }
  .tabset_underline { border-bottom: 1px solid #b9b9b9; }
  #tab1_content, #tab2_content, #tab3_content { padding: 5px; }
  </style>

</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_projects"], "$g_root_url/admin/projects/"),
                  array($project["name"], "../project.php"),
                  array($LANG["word_translations"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <?php require("../../../global/change_project_version_form.php"); ?>

  <h1><?=$LANG["word_translations"]?></h1>

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">

	  <div id="search_row" class="margin_bottom_large" style="margin-top: 10px">
	    <table cellspacing="0" cellpadding="1" width="100%">
	    <tr>
	    	<td width="100"><?=$LANG["word_filters"]?></td>
				<td>
				  <select name="language_id">
					  <?php
						foreach ($project_languages as $language_info)
						{
						  $curr_language_id   = $language_info["language_id"];
						  $curr_language_name = $language_info["language_name"];							
							$selected = ($curr_language_id == $language_id) ? "selected" : "";
						  echo "<option value=\"$curr_language_id\" {$selected}>$curr_language_name</option>\n";
						}
						?>
					</select>
				  <select name="category_id" tabindex="1">
				    <option value="all"><?=$LANG['label_all_categories']?></option>
						<?php
						foreach ($categories as $cat_info)
						{
						  $category_id = $cat_info["category_id"];
						  $category_name = $cat_info["category_name"];
						  $selected = ($g_category_id == $category_id) ? "selected" : "";
						  echo "<option value='$category_id' $selected>$category_name</option>\n";
						}
						?>
					</select>
				  <select name="data_size" tabindex="2">
				    <option <?php if ($g_data_size == "all") echo "selected"; ?> value="all"><?=$LANG["label_all_sizes"]?></option>
				    <option <?php if ($g_data_size == "words") echo "selected"; ?> value="words"><?=$LANG["word_words"]?></option>
				    <option <?php if ($g_data_size == "phrases") echo "selected"; ?> value="phrases"><?=$LANG["word_phrases"]?></option>
				    <option <?php if ($g_data_size == "sentences") echo "selected"; ?> value="sentences"><?=$LANG["word_sentences"]?></option>
				    <option <?php if ($g_data_size == "paragraphs") echo "selected"; ?> value="paragraphs"><?=$LANG["word_paragraphs"]?></option>
				    <option <?php if ($g_data_size == "documents") echo "selected"; ?> value="documents"><?=$LANG["word_documents"]?></option>
					</select>
	    	</td>
	    	<td width="80" rowspan="2">
	    		<input type="submit" name="search" value="<?=$LANG["word_search"]?>" tabindex="4" />
	    	</td>
	    </tr>
	    <tr>
	    	<td><?=$LANG["word_search"]?></td>
	    	<td>
			    <input type="text" style="width:98%" name="data_string"
			      value="<?php echo htmlspecialchars(stripslashes(@$request["data_string"])); ?>" tabindex="3" />
	    	</td>
	    </tr>
	    </table>
	  </div>

  </form>

  <?php


  // build values to pass along in nav query string
  $full_pass_along_str = "version_id=$version_id&order=$order";
  $pass_along_str = "version_id=$version_id";

  // display page navigation
  ot_display_page_nav($num_results, $results_per_page, $current_page, "order=$order");
  ?>

  <form action="" method="post" name="list_form">

    <table class="info margin_top" cellspacing="0" cellpadding="1" width="100%">
      <tr>
        <!-- <th width="25"> </th> -->
        <th>
	        <?php
	        if ($order == "category_id-DESC")
	          echo "<a href='{$_SERVER['PHP_SELF']}?order=category_id-ASC' class='black'>{$LANG['word_category']}</a>";
	        else
	          echo "<a href='{$_SERVER['PHP_SELF']}?order=category_id-DESC' class='black'>{$LANG['word_category']}</a>";
	        ?>
        </th>
        <th align="center"><?=$LANG['word_size']?></th>
        <th align="center" width="120"><?=$LANG['word_status']?></th>
        <th align="center" width="90"><?=$LANG['word_manage_uc']?></th>
      </tr>

      <?php
      while ($row = mysql_fetch_assoc($search_query))
      {
        $id             = $row["curr_data_id"];
				$translation_id = isset($row["translation_id"]) ? $row["translation_id"] : "";

        $precheck = "";
        if (in_array($id, $preselected_ids))
          $precheck = "checked";

        // <td align='center'><input type='checkbox' id='data_cb_$id' name='data[]' value='$id' onchange='select_row($id, $num_rows_in_page, \"JS\");' $precheck />&nbsp;</td>

        echo "<tr id='data_row_$id' height='24'>
              <td>{$row['category_name']}</td>
              <td>";

				if ($row['data_size'] == 1)
				  echo "<span class='data_size_word'>{$LANG['word_word']}</span>";
				else if ($row['data_size'] <= $g_PHRASE_SIZE)
				  echo "<span class='data_size_phrase'>{$LANG['word_phrase']}</span>";
				else if ($row['data_size'] <= $g_SENTENCE_SIZE)
				  echo "<span class='data_size_sentence'>{$LANG['word_sentence']}</span>";
				else if ($row['data_size'] <= $g_PARAGRAPH_SIZE)
				  echo "<span class='data_size_paragraph'>{$LANG['word_paragraph']}</span>";
				else
				  echo "<span class='data_size_document'>{$LANG['word_document']}</span>";

				echo "</td>";

        // if there isn't a translation, the translation is either in review or complete
        $status = "new";
        if (!empty($row["translation"]))
          $status = $row["translation_status"];

        // if this data is locked, only display a "LOCKED" string. Reviews are never locked.
        $translation_locked = false;
        $now = date("U");
        if (isset($row['lock_end']) && !empty($row['lock_end']) && $now < $row['lock_end'])
          $translation_locked = true;

        switch ($status)
        {
          case "new":
            $status_str = "<span class='status_new'>{$LANG['word_new']}</span>";
            if (!$translation_locked)
            {
              $bulk_translate_ids[] = $id;
              $translate_link = "<a href='translate.php?data_id=$id'>{$LANG['word_translate_uc']}</a>";
            }
            else
              $translate_link = "<span class='red'>{$LANG['word_locked_uc']}</span>";

            $review_link = "<span class='light_grey'>{$LANG['word_review_uc']}</span>";
            break;

          case "in_review":
            $status_str = "<span class='status_in_review'>{$LANG['label_in_review']}</span>";
            if (!$translation_locked)
              $translate_link = "<span class='light_grey'>{$LANG['word_translate_uc']}</span>";
            else
              $translate_link = "<span class='red'>{$LANG['word_locked_uc']}</span>";

			$bulk_review_ids[] = $id;
			$review_link = "<a href='review.php?data_id=$id'>{$LANG['word_review_uc']}</a>";
            break;

          case "completed":
            $status_str = "<span class='status_closed'>{$LANG['word_completed']}</span>";
            if (!$translation_locked)
              $translate_link = "<span class='light_grey'>{$LANG['word_translate_uc']}</span>";
            else
              $translate_link = "<span class='red'>{$LANG['word_locked_uc']}</span>";

            $review_link = "<a href='view.php?data_id=$id'>{$LANG['word_view_uc']}</a>";
            break;
        }

        $translation_truncated = mb_substr($row['data'], 0, 100) ;
        if (mb_strlen($row['data']) > 100)
          $translation_truncated .= "...";

        echo "<td>$status_str</td>";

        $data_is_export_only = $row["export_only"];

        $edit_filename = "edit_data.php";
        if ($data_is_export_only == "yes")
          $edit_filename = "edit_export_only_data.php";

        echo "<td align='center'><a href='$edit_filename?data_id=$id'>{$LANG['word_manage_uc']}</a></td>\n</tr>\n";

 				echo "</tr>\n";
      }
      ?>
      </table>


      <?php
      /*
      while ($row = mysql_fetch_assoc($search_query))
      {
        $id = $row["curr_data_id"];

        $precheck = "";
        if (in_array($id, $preselected_ids))
          $precheck = "checked";


        // display all the columns
        foreach ($settings_data_columns as $column)
        {
          switch ($column)
          {
            // ORDER column
            case "order":
              $order = $row["data_category_order"];
              echo "<td class='medium_grey'>$order</td>";
              break;

            // Category
            case "category":
              $category_name = $row["category_name"];
              echo "<td>$category_name</td>";
              break;

            // PHP Label
            case "php_label":
              $data_label = $row["data_label"];
              echo "<td>$data_label</td>";
              break;

            // specific language
            default:
              if (is_numeric($column))
              {
                if ($row["export_only"] == "yes")
                {
                  echo "<td class='light_grey'>{$LANG['word_na']}</td>";
                  break;
                }

                $review_count    = $row["language_{$column}_num_translations"];
                $trust_threshold = $project["trust_threshold"];

                // we + 1 to account for the original translation
                $td_cells = "";
                for ($i=0; $i<$trust_threshold+1; $i++)
                {
                  // give a little "umph" to the first dot to indicate it indicates something special
                  // (i.e. a translation has been made)
                  if ($i == 0)
                    $image = ($i >= $review_count) ? "grey_dot.jpg" : "green_dot.jpg";
                  else
                    $image = ($i >= $review_count) ? "grey_dot.jpg" : "light_green_dot.jpg";

                  $td_cells .= "<td><img src='/images/$image' /></td>\n";
                }
                echo "<td><table cellspacing='2' cellpadding='0'><tr>$td_cells</tr></table></td>";
              }
              break;
          }
        }

        $data_is_export_only = $row["export_only"];

        $edit_filename = "edit_data.php";
        if ($data_is_export_only == "yes")
          $edit_filename = "edit_export_only_data.php";

        echo "<td align='center'><a href='$edit_filename?data_id=$id'>{$LANG['word_manage_uc']}</a></td>\n</tr>\n";
      }
      */
      ?>
<!--
      <div style="padding-top: 5px; padding-bottom: 5px;">
        <input type="button" id="select_button" value="Select All On Page" onclick="select_all('JS');" />
        <input type="button" id="unselect_button" value="Unselect All" onclick="unselect_all('JS')" />
        <input type="button" value="Delete" onclick="delete_submissions()" class="burgundy" />
      </div>
-->
    </form>

    <p>
      <?php
      $query_str = "";
      if (isset($g_category_id) && !empty($g_category_id))
        $query_str = "?category_id=$g_category_id";
      ?>
      <form action="add_data.php<?=$query_str?>" method="post">
        <input type="submit" value="<?=$LANG['label_add_data']?>" class="bold" />
      </form>
    </p>


  <div class="hr"></div>

  <p>
    <a href="../project.php"><?=$LANG["label_backtoproject"]?></a>
  </p>

  <form method="POST" action="<?php echo $g_root_url?>/global/code/actions.php" name="hidden_form" target="hidden_iframe">
	<input type="hidden" name="action" value="" />
	<input type="hidden" name="version_id" value="<?=$version_id?>" />
	</form>

  <iframe src="about:blank" name="hidden_iframe" id="hidden_iframe" width="10" height="10" frameborder="0"></iframe>


<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>