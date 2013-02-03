<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');

require("_view.php");
?>
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = $LANG["word_administration"];
  require("$g_root_dir/global/header_code.php");
  ?>
</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($project["name"], "../project.php"),
                  array($LANG["word_translations"], "./"),
                  array($LANG["word_view"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["label_view_translation"]?></h1>

  <br />

  <div id="tab1_content">

	  <?php if ($allow_editing) { ?>
		  <div>Since no other translators have reviewed your translation yet, you may edit it below.</div>
			<br />
		<?php } ?>

    <form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="translation_form">
      <input type="hidden" name="data_id" value="<?=$request['data_id']?>" />
      <input type="hidden" name="translation_id" value="<?=$data['translation_id']?>" />

      <table cellspacing="0" cellpadding="2" width="100%">
      <tr>
        <td valign="top" class="bold pad_right" nowrap width="100">&nbsp;<?=$origin_language_name?></td>
        <td><div style="padding-bottom: 4px"><?=nl2br($data['data'])?></div></td>
      </tr>
      <tr>
        <td valign="top" class="bold pad_right translation_row" nowrap>&nbsp;<?=$target_language_name?></td>
        <td valign="top" class="pad_right translation_row">

				  <?php if ($allow_editing) { ?>

            <?php
            $textarea_height = "20px";
            if ($data["data_size"] > $g_PHRASE_SIZE)
              $textarea_height = "80px";
            if ($data["data_size"] > $g_SENTENCE_SIZE)
              $textarea_height = "120px";
            if ($data["data_size"] > $g_PARAGRAPH_SIZE)
              $textarea_height = "250px";
            ?>
            <textarea style="width:100%;height:<?=$textarea_height?>" name="translation"><?=$data['translation']?></textarea>

					<?php } else { ?>
  				  <div id="display_translation_div"><?=nl2br($data['translation'])?></div>
				  <?php } ?>

				</td>
      </tr>
      </table>

    <?php if ($allow_editing) { ?>
      <p>
        <input type="submit" name="edit_translation" value="UPDATE" />
      </p>
    <?php } ?>


		<br clear="all" />

    <div class="hr"></div>
    <p>
      <a href="index.php"><?=$LANG["label_backtotranslations"]?></a>
    </p>

  </div>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
