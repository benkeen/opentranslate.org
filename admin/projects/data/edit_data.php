<?php
session_start();
header("Cache-control: private");
require("_edit_data.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="<?=$LANG['direction']?>">
<head>

  <script src="../../../global/tiny_mce/tiny_mce.js"></script>
  <script>
    tinyMCE.init({
      mode : "exact",
      elements : "comments_for_translators",
      theme : "advanced",
      theme_advanced_toolbar_location : "top",
      theme_advanced_buttons1 : "bold,italic,underline,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,forecolor,link,unlink,hr",
      theme_advanced_buttons2 : "",
      theme_advanced_buttons3 : "",
      theme_advanced_toolbar_align : "left",
      theme_advanced_path_location : "bottom",
      theme_advanced_resize_horizontal : false,
      theme_advanced_resizing : true,
      height: "80",
      content_css : "/global/tinymce.css"
    });

    tinyMCE.init({
      mode : "<?=$tiny_mce_mode?>",
      elements : "data",
      theme : "advanced",
      theme_advanced_toolbar_location : "top",
      theme_advanced_buttons1 : "bold,italic,underline,separator,justifyleft,justifycenter,justifyright,separator,bullist,numlist,separator,outdent,indent,separator,forecolor,backcolor,separator,link,unlink,hr,separator,code",
      theme_advanced_buttons2 : "",
      theme_advanced_buttons3 : "",
      theme_advanced_toolbar_align : "left",
      theme_advanced_path_location : "bottom",
      theme_advanced_resize_horizontal : false,
      theme_advanced_resizing : true,
      height: "120",
      content_css : "/global/tinymce.css"
    });

  function toggle_editor(action)
  {
    if (action == "on")
      tinyMCE.execCommand('mceAddControl', false, "data");
    else
      tinyMCE.execCommand('mceRemoveControl', false, "data");
  }

  function delete_data(data_id)
  {
    var affected_versions = $("affected_versions").value;

    if (affected_versions == "single")
    {
	    if (confirm("<?=$LANG['validation_confirm_delete_data']?>"))
	    {
	      window.location = "<?=$_SERVER['PHP_SELF']?>?delete=" + data_id;
	    }
    }
    else
    {
      var selected_version_id = null;

      if (document.tab1.affected_version_id.length == undefined)
      {
        if (document.tab1.affected_version_id.checked)
          selected_version_id = document.tab1.affected_version_id.value;
      }
      else
      {
	      for (var i=0; i<document.tab1.affected_version_id.length; i++)
	      {
	        if (document.tab1.affected_version_id[i].checked)
	        {
	          selected_version_id = document.tab1.affected_version_id[i].value;
	          break;
	        }
	      }
      }

      if (selected_version_id == null)
      {
        alert("Please select a version from which this data item should be deleted.");
        return false;
      }

      if (confirm("Are you sure you want to delete this data item? It will be deleted for all child versions."))
      {
        window.location = "<?=$_SERVER['PHP_SELF']?>?delete=" + data_id + "&delete_version_id=" + selected_version_id;
      }
    }
  }

  var current_tab = <?=$curr_tab?>;
  </script>

  <?php
  $g_page_title = $LANG["word_administration"];
  require("$g_root_dir/global/header_code.php");
  ?>
  <script src="../../../global/general.js"></script>
	<script src="../../../global/manage_lists.js"></script>

  <style type="text/css">
  #data_tab1 {
    height: 26px;
    text-align: center;
    background-image: url(<?php if ($curr_tab == 1) echo '../../../images/tab_selected.jpg'; else echo '../../../images/tab_unselected.jpg'; ?>);
  }
  #data_tab2 {
    height: 26px;
    text-align: center;
    background-image: url(<?php if ($curr_tab == 2) echo '../../../images/tab_selected.jpg'; else echo '../../../images/tab_unselected.jpg'; ?>);
  }
  #data_tab3 {
    height: 26px;
    text-align: center;
    background-image: url(<?php if ($curr_tab == 3) echo '../../../images/tab_selected.jpg'; else echo '../../../images/tab_unselected.jpg'; ?>);
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
	                  array($LANG["word_data"], "./"),
	                  array($LANG["label_manage_data"], "")
	                    );
	require("$g_root_dir/global/templates/open_page.php");
	?>

  <?php
  $affected_version_ids = ot_get_data_usage_versions($version_id, $data_id);

  if (!empty($affected_version_ids))
  {
  ?>
	  <div style="float: right;">
	    <form action="<?php echo $_SERVER["PHP_SELF"]?>" method="post">
	      <input type="hidden" name="change_project_version" value="1" />
	      <input type="hidden" name="data_id" value="<?php echo $data_id; ?>" />
	  		<select name="version_id">
	  		<?php
	  		foreach ($affected_version_ids as $curr_version_id)
	  		{
	  			$version_name = ot_get_version_name($curr_version_id);
	  			$selected     = ($curr_version_id == $version_id) ? "selected" : "";
          echo "<option value=\"$curr_version_id\" $selected>$version_name</option>\n";
	  		}
	  		?>
	      </select><input type="submit" value="Select" />
	    </form>
	  </div>
  <?php
  }
  ?>

  <h1><?=$LANG["label_manage_data"]?></h1>

  <br />
  <?=ot_display_message($success, $message)?>

  <table cellspacing="0" cellpadding="0" summary="tab table" style="width: 100%; margin-bottom: 10px">
  <tr height="26">
    <td width="129" id="data_tab1"><a href="#" onclick="return change_tab(1);"><?=$LANG['word_data']?></a></td>
    <td width="2" class="tabset_underline"> </td>
    <td width="129" id="data_tab2"><a href="#" onclick="return change_tab(2);">Translation History</a></td>
    <td width="2" class="tabset_underline"> </td>
    <td width="129" id="data_tab3"><a href="#" onclick="return change_tab(3);">Questions</a></td>
    <td width="329" class="tabset_underline" align="<?=$LANG['align2']?>">
		</td>
  </tr>
  </table>

  <div id="tab1_content" <?php if ($curr_tab != 1) echo "style=\"display: none;\""; ?>>

    <form action="<?=$_SERVER['PHP_SELF']?>" name="tab1" method="post">
      <input type="hidden" name="data_id" value="<?=$request['data_id']?>" />

      <table cellspacing="2" cellpadding="1" width="100%" class="info">
      <tr>
        <td valign="top" class="red"> </td>
        <td><?=$LANG["label_php_label"]?></td>
        <td><input type="text" name="data_label" value="<?=htmlspecialchars($data['data_label'])?>" style="width:100%;" maxlength="255" /></td>
      </tr>
      <tr>
        <td width="20" class="red">*</td>
        <td><?=$LANG['label_data_type']?></td>
        <td>
          <input type="radio" name="use_html_editor" id="use_html_editor1" value="yes" onclick="toggle_editor('on')"
            <?php if ($data["use_html_editor"] == "yes") echo "checked"; ?> /><label for="use_html_editor1"><?=$LANG["word_html_uc"]?></label>
          <input type="radio" name="use_html_editor" id="use_html_editor2" value="no" onclick="toggle_editor('off')"
            <?php if ($data["use_html_editor"] == "no") echo "checked"; ?> /><label for="use_html_editor2"><?=$LANG["word_text"]?></label>
        </td>
      </tr>
      <tr>
        <td valign="top" class="red">*</td>
        <td valign="top"><?=$LANG["label_text_to_translate"]?></td>
        <td><textarea name="data" style="width: 100%; height: 50px"><?=$data["data"]?></textarea></td>
      </tr>
      <tr>
        <td valign="top" class="red">*</td>
        <td valign="top"><?=$LANG["word_category"]?></td>
        <td>
          <select name="category_id">
            <?php
            foreach ($categories as $category)
            {
              $category_id   = $category["category_id"];
              $category_name = $category["category_name"];
              $selected = ($data['category_id'] == $category_id) ? "selected" : "";
              echo "<option value='$category_id'{$selected}>$category_name</option>\n";
            }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td width="20" valign="top" class="red"> </td>
        <td valign="top" width="160"><?=$LANG["label_comments_for_translators"]?></td>
        <td><textarea name="comments_for_translators" style="width: 100%; height: 50px"><?=$data["comments_for_translators"]?></textarea></td>
      </tr>
      </table>

      <?php
      if (count($affected_version_ids) == 1)
      {
      ?>
        <input type="hidden" name="affected_version_id" value="<?=$version_id?>" />
        <input type="hidden" id="affected_versions" value="single" />

        <p>
	        <input type="submit" name="update_data" value="<?=$LANG['word_update']?>" />
	        <input type="button" class="burgundy" value="<?=$LANG['word_delete']?>" onclick="delete_data(<?=$data['data_id']?>)" />
	      </p>

      <?php
      }
      else
      {
      ?>

			<input type="hidden" id="affected_versions" value="multiple" />

			<div>
				<p>
				  This data is shared by multiple versions. Please indicate which versions should be affected by this update / deletion.
				  Any time you delete or update a version, the change will apply to all children.
				</p>

		  	<h4>Version Tree</h4>
		    <div id="data_version_tree">
			    <?php
            ot_display_data_usage_version_tree($version_id, $data_id, true);
			    ?>
		    </div>

        <?php
				// if this data was deleted from any children, display them here
				$versions = ot_get_versions_that_deleted_data($data_id);
				if (!empty($versions))
				{
				?>

        <div>This data was deleted from the following child version(s):</div>

        <ul>
        <?php
          foreach ($versions as $vid)
          {
          	echo "<li>" . ot_get_version_name($vid) . "</li>\n";
          }
				}
        ?>
        </ul>

		  </td>
        <input type="submit" name="update_data" value="<?=$LANG['word_update']?>" onclick="" />
        &nbsp;
        <input type="button" class="burgundy" value="<?=$LANG['word_delete']?>" onclick="delete_data(<?=$data['data_id']?>)" />

			</div>

      <?php
      }
      ?>

    </form>

  </div>

  <div id="tab2_content" <?php if ($curr_tab != 2) echo "style=\"display: none;\""; ?>>

    <div class="heading_2"><?=$LANG["label_translation_history"]?></div>
    <br />

    <?php
    if ($category_is_export_only)
    {
      echo "<div>{$LANG['text_category_export_only']}</div>";
    }
    else
    {
      ?>
      <table cellspacing="1" cellpadding="1" width="100%" class="info">
      <tr>
        <th><?=$LANG["word_language"]?></th>
        <th><?=$LANG["word_translation"]?></th>
        <th><?=$LANG["word_status"]?></th>
        <th><?=$LANG["word_progress"]?></th>
        <th width="90" align="center"><?=$LANG["word_history_uc"]?></th>
        <th width="90" align="center"><?=$LANG["word_edit_uc"]?></th>
      </tr>
      <?php
      foreach ($project["languages"] as $lang_info)
      {
        $display_count = 0;
        $translation_str = "<span class='light_grey'>{$LANG['label_no_translation']}</span>";
        $translation_status = "";
        $translation_id = "";

        foreach ($translations as $translation)
        {
          if ($lang_info['language_id'] != $translation["language_id"])
            continue;

          $display_count      = $translation["review_count"] + 1;
          $translation_status = $translation["translation_status"];
          $translation_id     = $translation["translation_id"];
          $translation_str = mb_substr($translation["translation"], 0, 50);

          if (mb_strlen($translation["translation"]) > 50)
            $translation_str .= "...";
        }

        $status = "<span class='status_new'>{$LANG['word_new']}</span>";
        switch ($translation_status)
        {
          case "in_review":
            $status = "<span class='status_in_review'>{$LANG['label_in_review']}</span>";
            break;
          case "completed":
            $status = "<span class='status_closed'>{$LANG['word_completed']}</span>";
            break;
        }

        if (empty($translation_id))
        {
          $history_link = "<span class='light_grey'>{$LANG['word_history_uc']}</span>";
          $edit_link = "<span class='light_grey'>{$LANG['word_edit_uc']}</span>";
        }
        else
        {
          $history_link = "<a href='view_history.php?translation_id=$translation_id&data_id=$data_id'>{$LANG['word_history_uc']}</a>";
          $edit_link    = "<a href='edit_translation.php?translation_id=$translation_id&data_id=$data_id'>{$LANG['word_edit_uc']}</a>";
        }

        echo "<tr>
          <td>{$lang_info['language_name']}</td>
          <td nowrap>$translation_str</td>
          <td>$status</td>
          <td>";

        $trust_threshold = $project["trust_threshold"];

        // we + 1 to account for the original translation
        $td_cells = "";
        for ($i=0; $i<$trust_threshold+1; $i++)
        {
          // give a little "umph" to the first dot to indicate it indicates something special
          // (i.e. a translation has been made)
          if ($i == 0)
            $image = ($i >= $display_count) ? "grey_dot.jpg" : "green_dot.jpg";
          else
            $image = ($i >= $display_count) ? "grey_dot.jpg" : "light_green_dot.jpg";

          $td_cells .= "<td><img src='/images/$image' /></td>\n";
        }
        echo "<table cellspacing='2' cellpadding='0'><tr>$td_cells</tr></table>";

        echo "
          </td>
          <td align='center'>$history_link</td>
          <td align='center'>$edit_link</td>
        </tr>
        ";
      }
      ?>
      </table>
    <?php
    }
    ?>

  </div>

  <div id="tab3_content" <?php if ($curr_tab != 3) echo "style=\"display: none;\""; ?>>

<?php if (count($data_questions) == 0) { ?>

    <div class="notify"><span><span><span><span><span><span><span><span>
      Nobody has contacted you with any questions.
    </span></span></span></span></span></span></span></span></div>

  <?php } else { ?>

    <div>
      This tab lists all correspondence from translators about this data. Each translator has a
      single conversation thread.
    </div>
    <br />

		<table class="info" width="100%" cellpadding="1" cellspacing="0">
		<tr>
			<th>Translator</th>
			<th>Subject</th>
			<th>Num Responses</th>
			<th>Date</th>
			<th width="70" align="center">VIEW</th>
		</tr>
    <?php
    $accounts = array();
    for ($i=0; $i<count($data_questions); $i++)
    {
      $question_id = $data_questions[$i]["question_id"];
      $data_id = $data_questions[$i]["data_id"];
      $data_info = ot_get_data($data_id);
      $version_id = $data_info["version_id"];
      $translator_id = $data_questions[$i]["account_id"];
      $subject = $data_questions[$i]["subject"];
			$creation_date = ot_get_date("", $data_questions[$i]["creation_date"], "M jS Y, g:i A");
      $num_responses = $data_questions[$i]["num_responses"];
      $unread_responses = $data_questions[$i]["unread_responses"];

      // we highlight the line as NEW if either there are new unread responses, or if the
      // question ITSELF is new
      $css_class = "";
      if ($unread_responses > 0)
      {
        $num_responses .= " (<b>$unread_responses</b> new)";
        $css_class = "highlight";
      }
      else if ($num_responses == 0 && $data_questions[$i]["status"] == "unread")
        $css_class = "highlight";

      // if we haven't already asked the database for information on this account, do so now
      if (!array_key_exists($account_id, $accounts))
        $accounts[$translator_id] = ot_get_account($translator_id);

      $translator_link = "<a href=\"../translators/edit.php?translator_id=$translator_id\">{$accounts[$translator_id]['first_name']} {$accounts[$translator_id]['last_name']}</a>";

      echo "
        <tr class='$css_class'>
          <td>$translator_link</td>
          <td>$subject</td>
          <td align='center'>$num_responses</td>
    			<td>$creation_date</td>
    			<td align='center'><a href='question_thread.php?data_id=$data_id&translator_id=$translator_id'>VIEW</a></td>
        </tr>";
    }
    ?>
    </table>

    <br />

  <?php } ?>

  </div>

  <div class="hr"></div>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>

