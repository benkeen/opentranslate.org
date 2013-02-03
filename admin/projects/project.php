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
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["word_projects"], "$g_root_url/admin/projects/"),
                  array($project["name"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <?php require("../../global/change_project_version_form.php"); ?>

  <h1><?=$project["name"];?></h1>

  <p><?=$LANG["text_project_dashboard"]?></p>

  <div>

  <h3>Main</h3>

  <div style="position: relative; height: 120px;">
    <div class="dashboard_option"
      onmouseover="this.style.backgroundImage = 'url(../../images/option_bg.jpg)'"
      onmouseout="this.style.backgroundImage = ''"
      onclick="window.location='data/'">
      <div style="position:relative">
        <div style="position:absolute; left: 8px;"><img src="../../images/Translations48x48.png"></div>
        <div style="margin-left: 65px; top: 15px; position:absolute;"><a href="data/" class="big_link"><?=$LANG["word_translations"]?></a></div>
      </div>
    </div>

    <div class="dashboard_option" style="left: 235px"
      onmouseover="this.style.backgroundImage = 'url(../../images/option_bg.jpg)'"
      onmouseout="this.style.backgroundImage = ''"
      onclick="window.location='translators/'">
      <div style="position:relative">
        <div style="position:absolute; left: 8px; top:1px"><img src="../../images/Person48x48.png"></div>
        <div style="margin-left: 65px; top: 15px; position:absolute;"><a href="translators/" class="big_link"><?=$LANG["word_translators"]?></a></div>
      </div>
    </div>

    <div class="dashboard_option" style="left: 470px"
      onmouseover="this.style.backgroundImage = 'url(../../images/option_bg.jpg)'"
      onmouseout="this.style.backgroundImage = ''"
      onclick="window.location='statistics/'">
      <div style="position:relative">
        <div style="position:absolute; left: 8px;"><img src="../../images/graph48x48.png"></div>
        <div style="margin-left: 65px; top: 15px; position:absolute;"><a href="statistics/" class="big_link"><?=$LANG["word_statistics"]?></a></div>
      </div>
    </div>

    <div class="dashboard_option" style="top: 60px"
      onmouseover="this.style.backgroundImage='url(../../images/option_bg.jpg)'; $('img_news').src='../../images/news48x48_blue.gif'"
      onmouseout="this.style.backgroundImage=''; $('img_news').src='../../images/news48x48_white.gif'"
      onclick="window.location='news/'">
      <div style="position:relative">
        <div style="position:absolute; left: 8px;"><img src="../../images/news48x48_white.gif" id="img_news" /></div>
        <div style="margin-left: 65px; top: 15px; position:absolute;"><a href="news/" class="big_link">News</a></div>
      </div>
    </div>

    <div class="dashboard_option" style="top: 60px; left: 235px"
      onmouseover="this.style.backgroundImage='url(../../images/option_bg.jpg)'; $('img_messages').src='../../images/messages48x48_blue.gif'"
      onmouseout="this.style.backgroundImage=''; $('img_messages').src='../../images/messages48x48_white.gif'"
      onclick="window.location='messages/'">
      <div style="position:relative">
        <div style="position:absolute; left: 8px;"><img src="../../images/messages48x48_white.gif" id="img_messages" /></div>
        <div style="margin-left: 65px; top: 15px; position:absolute;"><a href="messages/" class="big_link">Message Board</a></div>
      </div>
    </div>

  </div>


  <h3>Configuration</h3>

  <div style="position: relative; height: 120px;">

    <div class="dashboard_option"
      onmouseover="this.style.backgroundImage = 'url(../../images/option_bg.jpg)'"
      onmouseout="this.style.backgroundImage = ''"
      onclick="window.location='description.php'">
      <div style="position:relative">
        <div style="position:absolute; left:8px;"><img src="../../images/Description48x48.png"></div>
        <div style="margin-left: 65px; top: 15px; position:absolute;"><a href="description.php" class="big_link"><?=$LANG["label_project_description"]?></a></div>
      </div>
    </div>

    <div class="dashboard_option" style="left: 235px"
      onmouseover="this.style.backgroundImage = 'url(../../images/option_bg.jpg)'"
      onmouseout="this.style.backgroundImage = ''"
      onclick="window.location='translator_notes.php'">
      <div style="position:relative">
        <div style="position:absolute; left: 8px; top:1px"><img src="../../images/translator_notes.png"></div>
        <div style="margin-left: 65px; top: 15px; position:absolute;"><a href="translator_notes.php" class="big_link">Notes for Translators</a></div>
      </div>
    </div>

    <div class="dashboard_option" style="left: 470px"
      onmouseover="this.style.backgroundImage = 'url(../../images/option_bg.jpg)'"
      onmouseout="this.style.backgroundImage = ''"
      onclick="window.location='categories/'">
      <div style="position:relative">
        <div style="position:absolute; left: 8px;"><img src="../../images/Categories48x48.png"></div>
        <div style="margin-left: 65px; top: 15px; position:absolute;"><a href="categories/" class="big_link"><?=$LANG["word_categories"]?></a></div>
      </div>
    </div>

    <div class="dashboard_option" style="top: 60px;"
      onmouseover="this.style.backgroundImage = 'url(../../images/option_bg.jpg)'"
      onmouseout="this.style.backgroundImage = ''"
      onclick="window.location='versions/'">
      <div style="position:relative">
        <div style="position:absolute; left: 8px; top:1px"><img src="../../images/Versions48x48.png"></div>
        <div style="margin-left: 65px; top: 15px; position:absolute;"><a href="versions/" class="big_link"><?=$LANG["word_versions"]?></a></div>
      </div>
    </div>

    <div class="dashboard_option" style="top: 60px; left: 235px;"
      onmouseover="this.style.backgroundImage = 'url(../../images/option_bg.jpg)'"
      onmouseout="this.style.backgroundImage = ''"
      onclick="window.location='languages/'">
      <div style="position:relative">
        <div style="position:absolute; left: 8px;"><img src="../../images/babelfish48x48.png"></div>
        <div style="margin-left: 65px; top: 15px; position:absolute;"><a href="languages/" class="big_link">Languages</a></div>
      </div>
    </div>

    <div class="dashboard_option" style="top: 60px; left: 470px;"
      onmouseover="this.style.backgroundImage = 'url(../../images/option_bg.jpg)'"
      onmouseout="this.style.backgroundImage = ''"
      onclick="window.location='settings/'">
      <div style="position:relative">
        <div style="position:absolute; left: 8px;"><img src="../../images/Applications48x48.png"></div>
        <div style="margin-left: 65px; top: 15px; position:absolute;"><a href="settings/" class="big_link"><?=$LANG["word_settings"]?></a></div>
      </div>
    </div>
  </div>


  <h3>Data Management</h3>

  <div style="position: relative">

    <div class="dashboard_option"
      onmouseover="this.style.backgroundImage = 'url(../../images/option_bg.jpg)'"
      onmouseout="this.style.backgroundImage = ''"
      onclick="window.location='import/'">
      <div style="position:relative">
        <div style="position:absolute; left: 8px;"><img src="../../images/Import48x48.png"></div>
        <div style="margin-left: 65px; top: 15px; position:absolute;"><a href="import/" class="big_link"><?=$LANG["label_import_data"]?></a></div>
      </div>
    </div>

    <div class="dashboard_option" style="left: 235px"
      onmouseover="this.style.backgroundImage = 'url(../../images/option_bg.jpg)'"
      onmouseout="this.style.backgroundImage = ''"
      onclick="window.location='export/'">
      <div style="position:relative">
        <div style="position:absolute; left: 8px;"><img src="../../images/Export48x48.png"></div>
        <div style="margin-left: 65px; top: 15px; position:absolute;"><a href="export/" class="big_link"><?=$LANG["label_export_data"]?></a></div>
      </div>
    </div>

  </div>

  </div>


<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>