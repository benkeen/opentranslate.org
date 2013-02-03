<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');
require("_details.php");
?>
<html dir="<?=$LANG['direction']?>">
<head>
  <script language="javascript" type="text/javascript" src="/global/tiny_mce/tiny_mce.js"></script>
  <script language="javascript" type="text/javascript">
  tinyMCE.init({
    mode : "textareas",
    theme : "advanced",
    theme_advanced_buttons1 : "bold,italic,underline,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,outdent,indent,separator,forecolor,backcolor,separator,cut,copy,paste,separator,link,unlink,hr,fontsizeselect,separator,code",
    theme_advanced_buttons2 : "",
    theme_advanced_buttons3 : "",
    theme_advanced_toolbar_align : "left",
    theme_advanced_path_location : "bottom",
    theme_advanced_toolbar_location : "top",
    theme_advanced_resize_horizontal : false,
    theme_advanced_resizing : true,
    content_css : "/global/tinymce.css"
  });
  </script>

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
                  array($_SESSION["ot"]["project_name"], "../project.php"),
                  array("News", "index.php"),
                  array("News Item", "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1>News Item</h1>

  <br />

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
    <input type="hidden" name="news_id" value="<?=$news_id?>" />

    <table cellspacing="0" cellpadding="1" class="info" width="100%">
    <tr>
    	<td width="120">Subject</td>
    	<td><input type="text" name="subject" style="width: 100%" value="<?=htmlspecialchars($news["subject"])?>" /></td>
    </tr>
    <tr>
    	<td valign="top">Message</td>
    	<td><textarea name="message" style="width: 100%; height: 60px;"><?=$news["message"];?></textarea></td>
    </tr>
    </table>

    <p style="position: absolute;">
      <span style="position: absolute; left: 120px;"><input type="button" name="delete" value="DELETE" class="burgundy bold" onclick="window.location='index.php?delete=<?=$news_id?>" /></span>
      <input type="submit" name="update" value="UPDATE" />
    </p>

  </form>
  <br />
  <br />

  <div class="hr"></div>

  <br />

  <table cellspacing="0" cellpadding="1" class="info" width="500">
  <tr>
  	<th class="bold" width="50%">Not Read</th>
  	<th class="bold" width="50%">Read By</th>
  </tr>
  <tr>
    <td valign="top">
    <?php
      foreach ($project_translators as $translator)
      {
        $translator_id = $translator["translator_id"];

        if (in_array($translator_id, $read_list))
          continue;

        $name = "{$translator["first_name"]} {$translator["last_name"]}";
        echo "<div><a href='../translators/edit.php?translator_id=$translator_id'>$name</a></div>";
      }
    ?>
    </td>
    <td valign="top">

    <?php
      foreach ($project_translators as $translator)
      {
        $translator_id = $translator["translator_id"];

        if (!in_array($translator_id, $read_list))
          continue;

        $name = "{$translator["first_name"]} {$translator["last_name"]}";
        echo "<div><a href='../translators/edit.php?translator_id=$translator_id'>$name</a></div>";
      }
    ?>

    </td>
  </tr>
  </table>


<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
