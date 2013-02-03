<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');
require("_add_data.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="<?=$LANG['direction']?>">
<head>

  <script type="text/javascript" src="/global/tiny_mce/tiny_mce.js"></script>
  <script type="text/javascript">
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
    	mode : "none",
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

  function load_category_data(category_id)
  {
    data_info = map[category_id];

    for (i=0; i<data_info.length; i++)
    {
      val   = data_info[i][0];
      label = data_info[i][1];

      var new_option = document.createElement("option");
      new_option.text = label;
      new_option.value = val;
      $("insert_position_after").options.add(new_option);
    }
  }

  function change_category(new_cat_id)
  {
    $("insert_position_after").options.length = 0;
    load_category_data(new_cat_id);
  }


  // build the category => data JS map
  var map = new Array();
  <?php
  while (list($key, $value) = each($category_data))
  {
    echo "map[\"$key\"] = [ \n";

    $data = array();
    while (list($data_id, $data_label) = each($value))
      $data[] = "    [\"$data_id\", \"$data_label\"]";
    echo join(",\n", $data);

    echo "\n  ];\n";
  }
  ?>
  </script>

  <?php
  $g_page_title = $LANG["word_administration"];
  require("$g_root_dir/global/header_code.php");
  ?>
	<script type="text/javascript" src="/global/general.js"></script>

</head>
<body onload="load_category_data(document.add_data_form.category_id.value)">

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_projects"], "$g_root_url/admin/projects/"),
                  array($project["name"], "../project.php"),
                  array($LANG["word_data"], "./"),
                  array($LANG["label_add_data"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <?php require("../../../global/change_project_version_form.php"); ?>

  <h1><?=$project["name"]?>: <?=$LANG["label_add_data"]?></h1>

  <p><?=$LANG["text_add_data_page"]?></p>
  <br />

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="add_data_form">

    <table width="100%" cellpadding="2" cellspacing="1" class="info">
    <tr>
      <td width="20"></td>
      <td>Version</td>
      <td class="medium_grey">
        <?php
        echo ot_get_version_name($_SESSION["ot"]["version_id"]);
        ?>
        <input type="hidden" name="version_id" value="<?php echo $_SESSION["ot"]["version_id"]; ?>" />
      </td>
    </tr>
    <tr>
      <td width="20" valign="top" class="red"> </td>
      <td><?=$LANG['label_php_label']?></td>
      <td><input type="text" name="data_label" value="" style="width:100%;" maxlength="255" /></td>
    </tr>
    <tr>
      <td width="20" class="red">*</td>
      <td><?=$LANG['label_data_type']?></td>
      <td>
        <input type="radio" name="use_html_editor" id="use_html_editor1" value="yes" onclick="toggle_editor('on')" /><label for="use_html_editor1">HTML</label>
        <input type="radio" name="use_html_editor" id="use_html_editor2" value="no" onclick="toggle_editor('off')" checked /><label for="use_html_editor2">Text</label>
      </td>
    </tr>
    <tr>
      <td valign="top" class="red">*</td>
      <td valign="top"><?=$LANG['label_text_to_translate']?></td>
      <td><textarea name="data" id="data" style="width: 100%; height: 50px"></textarea></td>
    </tr>
    <tr>
      <td valign="top" class="red">*</td>
      <td><?=$LANG['word_category']?></td>
      <td>
        <select name="category_id" onchange="change_category(this.value)">
          <?php
          foreach ($categories as $category)
          {
            $category_id   = $category["category_id"];
            $category_name = $category["category_name"];
            $selected = (isset($_GET['category_id']) && $_GET['category_id'] == $category_id) ? "selected" : "";
            echo "<option value='$category_id'{$selected}>$category_name</option>\n";
          }
          ?>
        </select>
      </td>
    </tr>
    <tr>
      <td valign="top" class="red">*</td>
      <td valign="top"><?=$LANG["word_insert"]?></td>
      <td>

        <table cellpadding="0" cellspacing="1">
        <tr>
          <td><input type="radio" name="insert_position" id="ip2" value="start" /></td>
          <td><label for="ip2"><?=$LANG["label_at_start"]?></label></td>
        </tr>
        <tr>
          <td><input type="radio" name="insert_position" id="ip1" value="end" checked /></td>
          <td><label for="ip1"><?=$LANG["label_at_end"]?></label></td>
        </tr>
        <tr>
          <td><input type="radio" name="insert_position" id="ip3" value="after" /></td>
          <td><label for="ip3"><?=$LANG["word_after"]?></label>
            <select name="insert_position_after" id="insert_position_after" onchange="$('ip3').checked = true;">
            </select>
          </td>
        </tr>
        </table>

      </td>
    </tr>
    <tr>
      <td valign="top" class="red"> </td>
      <td valign="top" width="160">Comments for Translators</td>
      <td><textarea name="comments_for_translators" id="comments_for_translators" style="width: 100%; height: 50px"></textarea></td>
    </tr>
    </table>

    <p>
      <input type="submit" name="add_data" value="<?=$LANG['word_add']?>" />
    </p>

  </form>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>

