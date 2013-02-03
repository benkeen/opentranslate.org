<?php
if (!isset($page)) $page = "";
?>

<div id="public_nav_options" class="menu_section">
  <div class="nav_option_list">
    <div class="nav_section_title">Website</div>
    <div class="nav_option"><a href="<?=$g_root_url?>/"><?=$LANG["word_home"]?></a></div>
    <div class="nav_option"><a href="<?=$g_root_url?>/about.php"><?=$LANG["word_about"]?></a></div>
  </div>
</div>

<div id="admin_nav_options" class="menu_section">
  <div class="nav_option_list">
    <div class="nav_section_title">Administration</div>
    <?php
    if ($page == "dashboard")
      echo "<div class=\"nav_option_selected\">{$LANG["word_dashboard"]}</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"$g_root_url/translators/\">{$LANG["word_dashboard"]}</a></div>";

    if ($page == "my_account")
      echo "<div class=\"nav_option_selected\">{$LANG["label_my_account"]}</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"$g_root_url/translators/my_account.php\">{$LANG["label_my_account"]}</a></div>";
    ?>
  </div>
</div>

<?php
if (isset($_SESSION["ot"]["project_id"]))
{
  $project_name = $_SESSION["ot"]["project_name"];
?>

<div id="admin_nav_options" class="menu_section">
  <div class="nav_option_list">
    <div class="nav_section_title"><?=$project_name?></div>

    <?php
    if ($page == "project_dashboard")
      echo "<div class=\"nav_option_selected\">Project Dashboard</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"$g_root_url/translators/project.php\">Project Dashboard</a></div>";

    if ($page == "translate_now")
      echo "<div class=\"nav_option_selected\">Translate Now!</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"$g_root_url/translators/data/\">Translate Now!</a></div>";

    if ($page == "messages")
      echo "<div class=\"nav_option_selected\">Message Board</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"$g_root_url/translators/messages/\">Message Board</a></div>";

    if ($page == "notes_for_translators")
      echo "<div class=\"nav_option_selected\">Notes for Translators</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"$g_root_url/translators/translator_notes.php\">Notes for Translators</a></div>";

    if ($page == "project_description")
      echo "<div class=\"nav_option_selected\">{$LANG["label_project_description"]}</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"$g_root_url/translators/project_description.php\">{$LANG["label_project_description"]}</a></div>";

    if ($page == "statistics")
      echo "<div class=\"nav_option_selected\">{$LANG["word_statistics"]}</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"$g_root_url/translators/statistics.php\">{$LANG["word_statistics"]}</a></div>";

    if ($page == "settings")
      echo "<div class=\"nav_option_selected\">{$LANG["word_settings"]}</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"$g_root_url/translators/settings.php\">{$LANG["word_settings"]}</a></div>";

    if ($page == "contact")
      echo "<div class=\"nav_option_selected\">Contact Us</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"$g_root_url/translators/contact.php\">Contact Us</a></div>";

    ?>
  </div>
</div>

<?php
}
?>

<div id="admin_nav_options" class="menu_section">
  <div class="nav_option_list">
    <div class="nav_option"><a href="<?=$g_root_url?>/logout.php" id="logout_link"><?=$LANG["label_log_out"]?></a></div>
  </div>
</div>


