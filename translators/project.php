<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');

require("_project.php");
?>
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = $LANG["word_administration"];
  require("$g_root_dir/global/header_code.php");
  ?>
	<script type="text/javascript" src="/global/general.js"></script>
</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/translators/"),
                  array($project["name"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$project["name"]?></h1>

  <p><?=$LANG["text_translator_project_dashboard"]?></p>

  <h3>Main</h3>

  <div style="position: relative; height: 80px;">

    <div class="dashboard_option"
      onmouseover="this.style.backgroundImage='url(../images/option_bg.jpg)'; $('img_translations').src='../images/Translations48x48_blue.gif'"
      onmouseout="this.style.backgroundImage=''; $('img_translations').src='../images/Translations48x48_white.gif'"
      onclick="window.location='data/'">
      <div style="position:relative">
        <div style="position:absolute; left: 3px;"><img src="../images/Translations48x48_white.gif" id="img_translations" /></div>
        <div style="margin-left: 60px; top: 15px; position:absolute;"><a href="data/" class="big_link">Translate Now!</a></div>
      </div>
    </div>

    <div class="dashboard_option" style="left: 235px"
      onmouseover="this.style.backgroundImage='url(../images/option_bg.jpg)'; $('img_questions').src='../images/messages48x48_blue.gif'"
      onmouseout="this.style.backgroundImage=''; $('img_questions').src='../images/messages48x48_white.gif'"
      onclick="window.location='messages/'">
      <div style="position:relative">
        <div style="position:absolute; left: 2px;"><img src="../images/messages48x48_white.gif" id="img_questions" /></div>
        <div style="margin-left: 60px; top: 15px; position:absolute;"><a href="messages/" class="big_link">Message Board</a></div>
      </div>
    </div>

  </div>

  <br />
  <h3>Project Information</h3>

  <div style="position: relative; height: 120px;">

    <div class="dashboard_option"
      onmouseover="this.style.backgroundImage='url(../images/option_bg.jpg)'; $('img_description').src='../images/Description48x48_blue.gif'"
      onmouseout="this.style.backgroundImage=''; $('img_description').src='../images/Description48x48_white.gif'"
      onclick="window.location='project_description.php'">
      <div style="position:relative">
        <div style="position:absolute; left: 3px;"><img src="../images/Description48x48_white.gif" id="img_description" /></div>
        <div style="margin-left: 60px; top: 15px; position:absolute;"><a href="project_description.php" class="big_link">Project Description</a></div>
      </div>
    </div>

    <div class="dashboard_option" style="left: 235px"
      onmouseover="this.style.backgroundImage='url(../images/option_bg.jpg)'; $('img_translator_notes').src='../images/translator_notes_blue.gif'"
      onmouseout="this.style.backgroundImage=''; $('img_translator_notes').src='../images/translator_notes_white.gif'"
      onclick="window.location='translator_notes.php'">
      <div style="position:relative">
        <div style="position:absolute; left: 3px;"><img src="../images/translator_notes_white.gif" id="img_translator_notes" /></div>
        <div style="margin-left: 60px; top: 15px; position:absolute;"><a href="translator_notes.php" class="big_link">Notes for Translators</a></div>
      </div>
    </div>

    <div class="dashboard_option" style="left: 470px"
      onmouseover="this.style.backgroundImage='url(../images/option_bg.jpg)'; $('img_statistics').src='../images/graph48x48_blue.gif'"
      onmouseout="this.style.backgroundImage=''; $('img_statistics').src='../images/graph48x48_white.gif'"
      onclick="window.location='statistics.php'">
      <div style="position:relative">
        <div style="position:absolute; left: 3px;"><img src="../images/graph48x48_white.gif" id="img_statistics" /></div>
        <div style="margin-left: 60px; top: 15px; position:absolute;"><a href="statistics.php" class="big_link"><?=$LANG["word_statistics"]?></a></div>
      </div>
    </div>

    <div class="dashboard_option" style="top: 60px"
      onmouseover="this.style.backgroundImage='url(../images/option_bg.jpg)'; $('img_settings').src='../images/Applications48x48_blue.gif'"
      onmouseout="this.style.backgroundImage=''; $('img_settings').src='../images/Applications48x48_white.gif'"
      onclick="window.location='settings.php'">
      <div style="position:relative">
        <div style="position:absolute; left: 3px;"><img src="../images/Applications48x48_white.gif" id="img_settings" ></div>
        <div style="margin-left: 60px; top: 15px; position:absolute;"><a href="settings.php" class="big_link"><?=$LANG["word_settings"]?></a></div>
      </div>
    </div>

    <div class="dashboard_option" style="top: 60px; left: 235px"
      onmouseover="this.style.backgroundImage='url(../images/option_bg.jpg)'; $('img_contact').src='../images/contact48x48_blue.gif'"
      onmouseout="this.style.backgroundImage=''; $('img_contact').src='../images/contact48x48_white.gif'"
      onclick="window.location='contact.php'">
      <div style="position:relative">
        <div style="position:absolute; left: 3px;"><img src="../images/contact48x48_white.gif" id="img_contact" ></div>
        <div style="margin-left: 60px; top: 15px; position:absolute;"><a href="contact.php" class="big_link">Contact Us</a></div>
      </div>
    </div>

  </div>


<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>

