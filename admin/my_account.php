<?php
session_start();
header("Cache-control: private");
require("_my_account.php");
?>
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = $LANG["label_my_account"];
  require("$g_root_dir/global/header_code.php");
  ?>
</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["label_my_account"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["label_my_account"]?></h1>
  <br />

  <?=ot_display_message($success, $message)?>

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">

    <?php
    switch ($account_info["account_type"])
    {
      case "admin":
        require("account_admin.php");
        break;
      case "project_manager":
        require("account_project_manager.php");
        break;
    }
    ?>

    <p>
      <input type="submit" name="update_account" value="<?=$LANG['word_update']?>" />
    </p>

  </form>


<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
