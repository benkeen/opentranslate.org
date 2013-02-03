<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');

require("_delete.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = $LANG["label_edit_translator"];
  require("$g_root_dir/global/header_code.php");
  ?>
  <script type="text/javascript" src="/global/scriptaculous/lib/general.js"></script>
  <script type="text/javascript" src="/global/scriptaculous/lib/prototype.js"></script>
  <script type="text/javascript" src="/global/scriptaculous/src/scriptaculous.js"></script>

</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_translators"], "index.php"),
                  array("Delete Translator", "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1>Delete Translator: <?=$translator['first_name']?> <?=$translator['last_name']?></h1>
  <br />

  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
    <input type="hidden" name="translator_id" value="<?=$translator_id?>" />

    <div class="heading_2"><?=$LANG["label_account_info"]?></div>
    <br />

    <table class="info" width="600" cellpadding="1" cellspacing="0">
    <tr>
      <td width="180"><?=$LANG["word_status"]?></td>
      <td><?=$translator["status"]?></td>
    </tr>
    <tr>
      <td>Email</td>
      <td><?=$translator['email']?></td>
    </tr>
    <tr>
      <td valign="top"><?=$LANG["label_languages_spoken"]?></td>
      <td>

        <?php
        foreach ($languages as $language_info)
        {
          if (!in_array($language_info["language_id"], $translator["language_ids"]))
            continue;

          $language_name = $language_info["language_name"];
          echo "$language_name<br />";
        }
        ?>

      </td>
    </tr>
    <tr>
      <td>Last Logged In</td>
      <td><?=$translator['last_logged_in']?></td>
    </tr>
    <tr>
      <td>Total Translations</td>
      <td><?=$translator['total_translations']?></td>
    </tr>
    <tr>
      <td>Total Reviews</td>
      <td><?=$translator['total_reviews']?></td>
    </tr>
    <tr>
      <td>Total Review Points</td>
      <td><?=$translator['total_review_points']?></td>
    </tr>
    <tr>
      <td>Total Percent Reliable</td>
      <td><?=$translator['total_percent_reliable']?>%</td>
    </tr>
    <tr>
      <td>Total Translation Points</td>
      <td><?=$translator['total_translation_points']?></td>
    </tr>
    </table>

    <br />

    <div class="notify" style="width: 600px"><span><span><span><span><span><span><span><span>
      Are you sure you want to delete this translator? &nbsp;
      <input type="submit" name="delete_translator" value="Yes" class="burgundy" />
      <input type="button" value="No" onclick="window.location='index.php'"/>
    </span></span></span></span></span></span></span></span></div>

  </form>


  <div class="hr"></div>

  <p>
    <a href="index.php"><?=$LANG["label_backtotranslators"]?></a>
  </p>


<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
