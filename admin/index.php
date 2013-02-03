<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');
require("_index.php");
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
                  array($LANG["word_dashboard"], "")
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1><?=$LANG["word_dashboard"]?></h1>

  <?php
  if (empty($activity))
  {
    echo "<br />
          <div class='notify'><span><span><span><span><span><span><span><span>
            There hasn't been any recent activity in any of your projects in the last <b>$num_days</b> day(s).
          </span></span></span></span></span></span></span></span></div>";
  }
  else
  {
  ?>
    <p>
      The following table lists all translations made on your projects in the last <b><?=$num_days?></b>
      days.
    </p>

    <table cellpadding="1" cellspacing="0" class="info" width="100%">
    <tr>
      <th>Project</th>
      <th>Translator</th>
      <th>Num Translations</th>
      <th>Language</th>
    </tr>
    <?php
    $accounts   = array();
    $languages  = array();
    $versions   = array();
    for ($i=0; $i<count($activity); $i++)
    {
      $translator_id = $activity[$i]["translator_id"];
      $language_id   = $activity[$i]["language_id"];
      $version_id    = $activity[$i]["version_id"];

      // if we haven't already asked the database for information on this account, do so now
      if (!array_key_exists($translator_id, $accounts))
        $accounts[$translator_id] = ot_get_account($translator_id);

      if (!array_key_exists($language_id, $languages))
        $languages[$language_id] = ot_get_language_name($language_id);

      if (!array_key_exists($version_id, $versions))
        $version[$version_id] = ot_get_project_from_version_id($version_id);

      $project_id   = $version[$version_id]["project_id"];
      $project_name = $version[$version_id]["name"];

      $display_name = "{$accounts[$translator_id]["first_name"]} {$accounts[$translator_id]["last_name"]}";

      echo "<tr>
              <td><a href=\"projects/project.php?project_id=$project_id\">$project_name</a></td>
              <td><a href=\"projects/translators/edit.php?project_id=$project_id&translator_id=$translator_id\">$display_name</td>
              <td>{$activity[$i]["num_translations"]}</td>
              <td>{$languages[$language_id]}</td>
            </tr>";
    }
    ?>

    </table>

  <?php
  }
  ?>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
