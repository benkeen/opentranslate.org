<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');
require("_index.php");
?>
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = $LANG["word_translations"];
  require("$g_root_dir/global/header_code.php");
  ?>
	<script type="text/javascript" src="../../global/general.js"></script>
	<script type="text/javascript" src="../../global/manage_lists.js"></script>
  <script type="text/javascript">
  var current_tab = 1;
  </script>

  <style type="text/css">
  #data_tab1 {
    height:26px;
    width: 129px;
    text-align: center;
    background-image: url(../../images/tab_selected.jpg);
  }
  #data_tab2 {
    height: 26px;
    width: 129px;
    text-align: center;
    background-image: url(../../images/tab_unselected.jpg);
  }
  .tabset_underline { border-bottom: 1px solid #b9b9b9; }
  .tabset_between_tabs { border-bottom: 1px solid #b9b9b9; font-size: 4pt; }
  #tab1_content, #tab2_content { padding: 5px; }
  </style>

</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/translators"),
                  array($project["name"], "../project.php"),
                  array($LANG["word_translations"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <?php require("../../global/change_project_version_form.php"); ?>

	<div style="margin-bottom: 10px">
	  <div style="float:<?=$LANG['align2']?>" class="pad_right">
	    <b><?=$target_language_name?></b> -
	    <a href="<?=$_SERVER['PHP_SELF']?>?select=1"><?=$LANG["label_change_language"]?></a>
	  </div>
    <h1><?=$LANG["word_translations"]?></h1>
	</div>

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">

  <div id="search_row" class="margin_bottom">

    <table cellspacing="0" cellpadding="1" width="100%">
    <tr>
    	<td width="100"><?=$LANG["word_filters"]?></td>
    	<td>
			  <select name="type_filter" >
			    <option value="all" <?php if ($g_type_filter == "all") echo "selected"; ?>><?=$LANG["label_all_data"]?></option>
			    <option value="my_translations" <?php if ($g_type_filter == "my_translations") echo "selected"; ?>><?=$LANG["label_your_translations"]?></option>
					<?php
					// if the translator trust threshold is > 1, show the additional "new translations" and "review translations" options.
					if ($project["trust_threshold"] > 1) {
					?>
						<option value="needing_translation" <?php if ($g_type_filter == "needing_translation") echo "selected"; ?>><?=$LANG["label_needing_translation"]?></option>
						<option value="needing_review" <?php if ($g_type_filter == "needing_review") echo "selected"; ?>><?=$LANG["label_needing_review"]?></option>
					<?php
					}
					?>
		        <option value="open" <?php if ($g_type_filter == "open") echo "selected"; ?>><?=$LANG["label_anything_open"]?></option>
		        <option value="completed" <?php if ($g_type_filter == "completed") echo "selected"; ?>><?=$LANG["label_completed_translations"]?></option>
				</select>

			  <select name="category_id">
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

			  <select name="data_size">
			    <option <?php if ($g_data_size == "all") echo "selected"; ?> value="all"><?=$LANG["label_all_sizes"]?></option>
			    <option <?php if ($g_data_size == "words") echo "selected"; ?> value="words"><?=$LANG["word_words"]?></option>
			    <option <?php if ($g_data_size == "phrases") echo "selected"; ?> value="phrases"><?=$LANG["word_phrases"]?></option>
			    <option <?php if ($g_data_size == "sentences") echo "selected"; ?> value="sentences"><?=$LANG["word_sentences"]?></option>
			    <option <?php if ($g_data_size == "paragraphs") echo "selected"; ?> value="paragraphs"><?=$LANG["word_paragraphs"]?></option>
			    <option <?php if ($g_data_size == "documents") echo "selected"; ?> value="documents"><?=$LANG["word_documents"]?></option>
				</select>
    	</td>
    	<td width="80" rowspan="2">
    		<input type="submit" name="search" value="<?=$LANG["word_search"]?>" />
    	</td>
    </tr>
    <tr>
    	<td><?=$LANG["word_search"]?></td>
    	<td>
		    <input type="text" style="width:98%" name="data_string" value="<?php echo htmlspecialchars(stripslashes(@$request["data_string"])); ?>" />
    	</td>
    </tr>
    </table>
  </div>

  </form>

  <table cellspacing="0" cellpadding="0" summary="tab table" style="width: 100%; margin-bottom: 10px">
  <tr height="26">
    <td width="129" id="data_tab1"><a href="#" onclick="return change_tab(1);"><?=$LANG['word_data']?></a></td>
    <td width="2" class="tabset_between_tabs">&nbsp;</td>
    <td width="129" id="data_tab2"><a href="#" onclick="return change_tab(2);"><?=$LANG['word_help']?></a></td>
    <td class="tabset_underline"> </td>
  </tr>
  </table>

  <div id="tab1_content">

  <?php
  // if there's no data for this project, don't show anything.
  if ($num_results == 0)
  {
  ?>

  <p>
	  <div class="notify">
		  <span><span><span><span><span><span><span><span>
				<div style="padding: 5px;">
  				<?=$LANG["text_translator_no_search_results"]?>
				</div>
			</span></span></span></span></span></span></span></span>
		</div>
	</p>

  <br />

  <?php
  } else {
  ?>

    <?php
    // display page navigation
    ot_display_page_nav($num_results, $results_per_page, $current_page, "order=$order");
    ?>

    <form action="" method="post" name="list_form">

      <table class="info" cellspacing="0" cellpadding="1" width="100%">
      <tr>
        <th style='padding-right: 10px;'>
        <?php
        if ($order == "category_id-DESC")
          echo "<a href='{$_SERVER['PHP_SELF']}?order=category_id-ASC' class='black'>{$LANG['word_category']}</a>";
        else
          echo "<a href='{$_SERVER['PHP_SELF']}?order=category_id-DESC' class='black'>{$LANG['word_category']}</a>";
        ?>
        </th>
        <th align='center'><?=$LANG['word_size']?></th>
        <th align='center' width="120"><?=$LANG['word_status']?></th>
        <th align='center' width="90"><?=$LANG['word_translate_uc']?></th>
        <th align='center' width="90"><?=$LANG['word_review_uc']?></th>
      <!--   <th align='center' width="90"><?=$LANG['word_preview_uc']?></th> -->
      </tr>

      <?php
		
      $bulk_translate_ids = array();
      $bulk_review_ids    = array();
      while ($row = mysql_fetch_assoc($search_query))
      {
        $id             = $row["curr_data_id"];
				$translation_id = isset($row["translation_id"]) ? $row["translation_id"] : "";
				
        $precheck = "";
        if (in_array($id, $preselected_ids))
          $precheck = "checked";

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
						{
						  // we DO allow translators to update their translations provided that (a) they were the one that originally 
							// translated it and (b) there have been no reviews made
							if (($row["translator_id"] == $g_google_translator_id) ||
							    ($row["translator_id"] == $account_id && $row["last_reviewer_id"] == $account_id)) 
							{
                $bulk_translate_ids[] = $id;
                $translate_link = "<a href='translate.php?data_id=$id'>UPDATE</a>";
								
								if ($row["translator_id"] == $g_google_translator_id)
								  $translate_link .= " <small>(g)</small>";							
							}
							else
                $translate_link = "<span class='light_grey'>{$LANG['word_translate_uc']}</span>";
					  }
            else
              $translate_link = "<span class='red'>{$LANG['word_locked_uc']}</span>";

            // if this translator is the original translator or if they've already reviewed it, show
            // a VIEW link instead of the REVIEW. That page lets them see the translation but not
            // edit it
            if (!empty($translation_id) && (in_array($translation_id, $reviewed_translation_ids) || $account_id == $row["translator_id"]))
              $review_link = "<a href='view.php?data_id=$id'>{$LANG['word_view_uc']}</a>";
            else
            {
              $bulk_review_ids[] = $id;
              $review_link = "<a href='review.php?data_id=$id'>{$LANG['word_review_uc']}</a>";
            }
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
        echo "<td align='center'>$translate_link</td>\n";
        echo "<td align='center'>$review_link</td>\n";
/*
         echo "<td align='center'>
                <a href='#' onmouseout='popUp(event, \"t{$id}\")' onmouseover='popUp(event, \"t{$id}\")' onclick='return false'>{$LANG['word_preview_uc']}</a>
                <div class='tip' id='t{$id}'>$translation_truncated</div>
              </td>\n";
*/

 				echo "</tr>\n";
      }
      $_SESSION["ot"]["project"]["bulk_translate_data_ids"] = $bulk_translate_ids;
      $_SESSION["ot"]["project"]["bulk_review_data_ids"]    = $bulk_review_ids;


      if (empty($_SESSION["ot"]["project"]["bulk_review_data_ids"])) $bulk_review_disabled = "disabled";
      ?>
      </table>

      <div style="padding-top: 5px; padding-bottom: 5px;">
        <?php

        if (empty($_SESSION["ot"]["project"]["bulk_translate_data_ids"]))
          echo "<input type='button' value='{$LANG['label_translate_all_on_page']}' class='medium_grey' disabled />";
        else
          echo "<input type='button' value='{$LANG['label_translate_all_on_page']}' class='blue' onclick=\"window.location='bulk_translate.php'\" />";

        if (empty($_SESSION["ot"]["project"]["bulk_review_data_ids"]))
          echo "<input type='button' value='{$LANG['label_review_all_on_page']}' class='medium_grey' disabled />";
        else
          echo "<input type='button' value='{$LANG['label_review_all_on_page']}' class='blue' onclick=\"window.location='bulk_review.php'\" />";

        ?>
      </div>

    </form>

  <?php
  }
  ?>
  </div>

  <div id="tab2_content" style="display: none;">

	  <h3>What is this page?</h3>
    <p>
		  This page contains all text available for translation. The search panel above lets you filter 
			what text you'd like to see - by size, category, status and/or by a custom search string.
		</p>
		
		<p>
		  Click on the <span class="blue">TRANSLATE</span> or <span class="blue">REVIEW</span> links to 
			translate or review the items. When a translation has been fully reviewed, you will see 
			a <span class="blue">VIEW</span> link. That will allow you to view the original text and 
			its final translation.
	  </p>
		
		<p>
		  You can either translate and review each data item individually by clicking the 
			appropriate <span class="blue">TRANSLATE</span> or <span class="blue">REVIEW</span> link or 
			click the "Review all on Page" or "Translate all on Page" buttons at the bottom of the page.
		</p>

		<p>
		  If this project has multiple versions, you'll see a dropdown at the top of the page 
			which will let you select the version data you want to translate. Text that is shared across 
			multiple versions may be translated in ANY versions: your translations will automatically apply to
			all relevant versions. 
		</p>

		<p>
		  <small>(g)</small> indicates that it was an automatic translation by Google Translate, hence
			it is likely to contain errors.
		</p>
		
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

