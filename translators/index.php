<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');

require("_index.php");
?>
<html dir="<?=$LANG['direction']?>">
<head>
  <?php
  $g_page_title = "";
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
  if (count($projects) == 0)
  {
    if (empty($translator_project_languages))
		{
      echo <<<EOF
			    <br />
					<div class='notify'><span><span><span><span><span><span><span><span>
  		    Welcome to Open Translate! Click on the My Account link in the navigation
					menu to specify which languages you speak. After that, return here to the 
					dashboard to see what projects need translation work. 
  				</span></span></span></span></span></span></span></span></div>
EOF;
		}
		else
		{
      echo "<br /><div class='notify'><span><span><span><span><span><span><span><span>"
  		    . $LANG["text_translators_no_projects"]
  				. "</span></span></span></span></span></span></span></span></div>";
		}
  }
  else
  {
  ?>

    <p><?=$LANG["text_translators_projects"]?></p>

    <table class="info" width="100%" cellpadding="1" cellspacing="0">
    <tr>
      <th width="30" align="center"><?=$LANG["word_id_uc"]?></th>
      <th><?=$LANG["label_project_name"]?></th>
      <th><?=$LANG["word_translations"]?></th>
      <th width="150" align="center"><?=$LANG["label_last_modified"]?></th>
  	  <th width="70" align="center"><?=$LANG["word_select_uc"]?></th>
    </tr>

    <?php
    foreach ($projects as $project)
    {
      $project_id = $project["project_id"];
			$origin_language = ot_get_language_name($project["origin_language_id"]);

      // get a list of all language pairs that this person has volunteered to help translate
			// (e.g. English - French, English - Swahili)
      $language_str_arr = array();
			foreach ($translator_project_languages[$project_id] as $curr_project_id)
			{
			  $target_language = ot_get_language_name($curr_project_id);
			  $language_str_arr[] = "$origin_language - $target_language";
      }
      $language_str = join("<br />", $language_str_arr);

      // format dates
    	$last_modified = ot_get_date("", $project["project_last_modified"], "M jS Y, g:i A");

      echo "<tr>
              <td valign='top' align='center' class='blue bold'>$project_id</td>
              <td valign='top'>{$project['name']}</td>
              <td>$language_str</td>
              <td valign='top'>$last_modified</td>
  					  <td valign='top' align='center'><a href='project.php?project_id={$project["project_id"]}'>{$LANG["word_select_uc"]}</a></td>
            </tr>";
    }
    ?>
    </table>

  <?php
  }
  ?>

  <br />

  <?php
  $first_row = true;
  foreach ($available_projects as $project)
  {
  	$creation_date = ot_get_date("", $project["creation_date"], "M jS Y, g:i A");
    $project_id    = $project["unique_project_id"];
    $origin_language = ot_get_language_name($project["origin_language_id"]);

    // get a list of all language pairs needing translation (e.g. English - French, English - Swahili)
    $language_str_arr = array();
    foreach ($project["languages"] as $lang_id)
    {
		  if ($lang_id == $project["origin_language_id"])
			  continue;

			// if this translator is already translating between these two languages for this project,
			// don't show the language pair.
		  if (key_exists($project_id, $translator_project_languages))
			{
			  if (in_array($lang_id, $translator_project_languages[$project_id]))
				  continue;
			}

      $source_language = ot_get_language_name($lang_id);
      $language_str_arr[] = "$origin_language - $source_language";
    }

		// if there are NO languages pairs to show for this project, the translator has already signed up
		// for them all. Don't show the row!
		if (empty($language_str_arr))
		  continue;

    if ($first_row)
    {
    ?>

  <h3><?=$LANG["label_available_projects"]?></h3>

	<p><?=$LANG["text_available_projects"]?></p>

  <table class="info" width="100%" cellpadding="1" cellspacing="0">
  <tr>
    <th width="30" align="center"><?=$LANG["word_id_uc"]?></th>
    <th><?=$LANG["word_project"]?></th>
    <th><?=$LANG["label_translations_needed"]?></th>
    <th><?=$LANG["label_creation_date"]?></th>
    <th width="70" align='center'><?=$LANG["word_details_uc"]?></th>
  </tr>

    <?php
      $first_row = false;
    }
    $language_str = join("<br />", $language_str_arr);

    echo "
    <tr>
      <td align='center' class='blue bold' valign='top'>$project_id</td>
      <td valign='top'>{$project['name']}</td>
      <td>$language_str</td>
      <td valign='top'>$creation_date</td>
      <td valign='top' align='center'><a href='description.php?project_id=$project_id'>{$LANG['word_details_uc']}</a></td>
    </tr>";
  }

  // if first row is still true, that means there weren't any projects available
  if (!$first_row)
    echo "</table>";
  ?>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
