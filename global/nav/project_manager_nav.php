<?php
if (!isset($page)) $page = "";
?>

<div id="public_nav_options" class="menu_section">
  <div class="nav_option_list">
    <div class="nav_section_title">Website</div> 
    <div class="nav_option"><a href="/"><?=$LANG["word_home"]?></a></div>
    <div class="nav_option"><a href="/about.php"><?=$LANG["word_about"]?></a></div>
  </div>
</div>

<div id="admin_nav_options" class="menu_section">
  <div class="nav_option_list">
    <div class="nav_section_title">Administration</div> 
    <?php
    if ($page == "dashboard")
      echo "<div class=\"nav_option_selected\">{$LANG["word_dashboard"]}</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"/admin/\">{$LANG["word_dashboard"]}</a></div>";

    if ($page == "my_account")
      echo "<div class=\"nav_option_selected\">{$LANG["label_my_account"]}</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"/admin/my_account.php\">{$LANG["label_my_account"]}</a></div>";

    if ($page == "projects")
      echo "<div class=\"nav_option_selected\">{$LANG["word_projects"]}</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"/admin/projects/\">{$LANG["word_projects"]}</a></div>";
    ?>
  </div>
</div>

<?php
if (isset($_SESSION["ot"]["project_id"]))
{
  $project_name = $_SESSION["ot"]["project_name"]; 
  $project_id   = $_SESSION["ot"]["project_id"];
?>

<div id="admin_nav_options" class="menu_section">
  <div class="nav_option_list">
    <div class="nav_section_title"><?=$project_name?></div> 
    <?php
    if ($page == "project_dashboard")
      echo '<div class="nav_option_selected">Project Dashboard</div>';
    else
      echo '<div class="nav_option"><a href="/admin/projects/project.php">Project Dashboard</a></div>';

    if ($page == "translations")
      echo "<div class=\"nav_option_selected\">{$LANG["word_translations"]}</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"/admin/projects/data/\">{$LANG["word_translations"]}</a></div>";

    if ($page == "translators")
      echo "<div class=\"nav_option_selected\">{$LANG["word_translators"]}</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"/admin/projects/translators/\">{$LANG["word_translators"]}</a></div>";

    if ($page == "statistics")
      echo "<div class=\"nav_option_selected\">{$LANG["word_statistics"]}</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"/admin/projects/statistics/\">{$LANG["word_statistics"]}</a></div>";

    if ($page == "news")
      echo "<div class=\"nav_option_selected\">News</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"/admin/projects/news/\">News</a></div>";

    if ($page == "messages")
      echo "<div class=\"nav_option_selected\">Message Board</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"/admin/projects/messages/\">Message Board</a></div>";

    if ($page == "project_description")
      echo "<div class=\"nav_option_selected\">{$LANG["label_project_description"]}</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"/admin/projects/description.php\">{$LANG["label_project_description"]}</a></div>";

    if ($page == "notes_for_translators")
      echo "<div class=\"nav_option_selected\">Notes for Translators</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"/admin/projects/translator_notes.php\">Notes for Translators</a></div>";

    if ($page == "categories")
      echo "<div class=\"nav_option_selected\">{$LANG["word_categories"]}</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"/admin/projects/categories/\">{$LANG["word_categories"]}</a></div>";

    if ($page == "versions")
      echo "<div class=\"nav_option_selected\">{$LANG["word_versions"]}</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"/admin/projects/versions/\">{$LANG["word_versions"]}</a></div>";

    if ($page == "settings")
      echo "<div class=\"nav_option_selected\">{$LANG["word_settings"]}</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"/admin/projects/settings/\">{$LANG["word_settings"]}</a></div>";

    if ($page == "import")
      echo "<div class=\"nav_option_selected\">Import</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"/admin/projects/import/\">Import</a></div>";

    if ($page == "export")
      echo "<div class=\"nav_option_selected\">Export</div>";
    else
      echo "<div class=\"nav_option\"><a href=\"/admin/projects/export/\">Export</a></div>";
    ?>
  </div>
</div>

<?php
}
?>

<div id="admin_nav_options" class="menu_section">
  <div class="nav_option_list">
    <div class="nav_option"><a href="/logout.php" id="logout_link"><?=$LANG["label_log_out"]?></a></div>
  </div>
</div>    
