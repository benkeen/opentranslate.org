<?php

/*------------------------------------------------------------------------------------------------*\
  Copyright (C) 2010 Benjamin Keen
  http://www.opentranslate.org
\*------------------------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------------------------*\
  STATISTICS - functions in brief

    Project Statistics

      ot_get_percent_translated
      ot_update_percent_translated
      ot_get_percent_reliable
      ot_update_percent_reliable
      ot_get_project_version_statistics
  		ot_update_all_projects_percent_translated
      ot_update_all_projects_percent_reliable
      ot_update_project_statistics

    Translator Statistics

      ot_get_translator_points
      ot_get_all_translator_points
      ot_update_translation_points
      ot_update_translation_reliability_percentage
      ot_update_review_points
  		ot_update_total_translators_stats
      ot_calculate_points

\*------------------------------------------------------------------------------------------------*/

function ot_get_percent_translated($version_id, $language_id)
{

}


/**
 * Updates the percent translated score for a particular version-language.
 */
function ot_update_percent_translated($version_id, $language_id)
{
  // count up the total data available for translation in this version. Only count those data
  // items that belong in categories that are available for exporting and that haven't been
  // deleted anywhere along the version chain
  $total_num_data = ot_get_num_version_data($version_id);

  // now find out how many translations have been made for this language
  $num_translations = ot_get_num_version_data_translations($version_id, $language_id);

  // do the math
	$percentage = floor(($num_translations / $total_num_data) * 100);

//	echo ot_get_language_name($language_id) . ": $num_translations / $total_num_data = $percentage%<br />";

  // update the percent_translated field in tr_project_version_languages
	mysql_query("
    UPDATE tr_project_version_language_stats
  	SET    percent_translated = $percentage
  	WHERE  version_id = $version_id AND
  	       language_id = $language_id
					   ") or ot_handle_error(mysql_error());
}


/**
 * Called on a cron job; this sifts through all the translations for a version-language and
 * calculates the total reliability percentage based on the number of reviews of the translations
 * that have been made.
 *
 * Reliability percentage is based on the translations that have been made and NOT of those
 * translations that have yet to be made: the "incompleteness" of the total data translations is
 * reflected by the "percent translated" value. In other words, if there's one translation made
 * of a single item.
 */
function ot_update_percent_reliable($version_id, $language_id)
{
  // get the trust threshold for this project
  $trust_query = mysql_query("
    SELECT p.trust_threshold
    FROM   tr_projects p, tr_project_versions pv
    WHERE  p.project_id = pv.project_id AND
           pv.version_id = $version_id
    LIMIT 1
      ");

  $result = mysql_fetch_assoc($trust_query);
  $trust_threshold = $result["trust_threshold"];

  // if the trust threshold is set to 0 (Not Applicable), the percent reliable value should
  // already be set to 100%. [TODO When adding new language to project, should
  // set the percent reliable value to 100% if trust threshold == 0]
  if ($trust_threshold == 0)
    return;


  // now get all translations made for this version-language
  $translation_info = mysql_query("
    SELECT review_count
    FROM   tr_data_translations dt, tr_data d
    WHERE  dt.data_id = d.data_id AND
           d.version_id = $version_id AND
           dt.language_id = $language_id
      ") or die(mysql_error());

  $review_counts = array();
  while ($row = mysql_fetch_assoc($translation_info))
    $review_counts[] = $row["review_count"];

  // if there are no reviews, set the reliability percentage to 0% - this seems clearer than 100%
  if (count($review_counts) == 0)
  {
    $percent_reliability = 0;
  }
  else
  {
    // do the math
    $total_percentages = 0;
    $single_review_percentage = round(100 / $trust_threshold);
    foreach ($review_counts as $review)
    {
      if ($review >= $trust_threshold)
        $total_percentages += 100;
      else
        $total_percentages += ($review * $single_review_percentage);
    }

    // now compute the average (i.e. percent reliability)
    $percent_reliability = $total_percentages / count($review_counts);
  }

  $translation_info = mysql_query("
    UPDATE tr_project_version_language_stats
    SET    percent_reliability = $percent_reliability
    WHERE  version_id = $version_id AND
           language_id = $language_id
      ") or die(mysql_error());
}


/**
 * Returns the percent reliability, percent translated values for a project version, for all
 * languages it's being translated into. Used on the admin statistics page, PHP export data page and
 * maybe others.
 *
 * Returns a mysql result set of all languages in this project version, sorted by language name.
 */
function ot_get_project_version_statistics($version_id)
{
  $query = mysql_query("
    SELECT *
    FROM   tr_project_version_language_stats pvls
      INNER JOIN tr_languages l ON pvls.language_id = l.language_id
    WHERE  pvls.version_id = $version_id
    ORDER BY l.language_name
      ") or die(mysql_error());

  return $query;
}


function ot_update_all_projects_percent_translated()
{

}


function ot_update_all_projects_percent_reliable()
{

}


/**
 * Updates the statistics for all project-version-languages.
 */
function ot_update_project_statistics($project_id)
{
  $versions = ot_get_project_versions($project_id);
  $project_languages = ot_get_project_languages($project_id);

  foreach ($versions as $version_info)
  {
    $version_id = $version_info["version_id"];

    foreach ($project_languages as $info)
    {
      ot_update_percent_translated($version_id, $info["language_id"]);
      //ot_update_percent_reliable($version_id, $info["language_id"]);
    }
  }
}


// -------------------------------------------------------------------------------------------------



/**
 * Returns the translator points record for a translator, origin language ID and target language ID.
 *
 * In case the record doesn't exist, it CREATES it. I couldn't think of a better name for the function
 * to sum up this additional functionality...
 */
function ot_get_translator_points($translator_id, $origin_language_id, $target_language_id)
{
  // see if a record already exists for this translator-language
  $query = mysql_query("
    SELECT *
    FROM   tr_translator_points
    WHERE  translator_id = $translator_id AND
           origin_language_id = $origin_language_id AND
           target_language_id = $target_language_id
             ");

  // if no record existed, add it - and then immediately query the database again to retrieve the blank row
  if (mysql_num_rows($query) == 0)
  {
    mysql_query("
      INSERT INTO tr_translator_points (translator_id, origin_language_id,
        target_language_id, percent_reliability, num_peer_reviews, num_translations, num_reviews, review_points, translation_points)
      VALUES ($translator_id, $origin_language_id, $target_language_id, 100, 0, 0, 0, 0, 0)
         ");

    $query = mysql_query("
      SELECT *
      FROM   tr_translator_points
      WHERE  translator_id = $translator_id AND
             origin_language_id = $origin_language_id AND
             target_language_id = $target_language_id
               ");
  }

  return mysql_fetch_assoc($query);
}


/**
 * Returns all language-specific statistics for a translator.
 */
function ot_get_all_translator_points($translator_id)
{
  $query = mysql_query("
    SELECT *
    FROM   tr_translator_points
    WHERE  translator_id = $translator_id
      ");

  $info = array();
  while ($row = mysql_fetch_assoc($query))
    $info[] = $row;

  return $info;
}


/**
 * Updates translation points for a translator, between two distinct languages.
 */
function ot_update_translation_points($translator_id, $origin_language_id, $target_language_id, $data_size)
{
  $points_info = ot_get_translator_points($translator_id, $origin_language_id, $target_language_id);
  $num_translations = $points_info["num_translations"];
  $old_points = $points_info["translation_points"];

  $num_translations++;
  $new_points = ot_calculate_points("translation", $data_size);
  $updated_points = $new_points + $old_points;

  // update the points info
  mysql_query("
      UPDATE tr_translator_points
      SET    num_translations = $num_translations,
             translation_points = $updated_points
      WHERE  translator_id = $translator_id AND
             origin_language_id = $origin_language_id AND
             target_language_id = $target_language_id
    ") or ot_handle_error(mysql_error());
}


/**
 * Called implicitly by reviewers to update the PREVIOUS translator/reviewer's translation reliability percentage.
 */
function ot_update_translation_reliability_percentage($translator_id, $origin_language_id, $target_language_id, $rating)
{
  $score["excellent"] = 100;
  $score["good"]      = 75;
  $score["fair"]      = 50;
  $score["poor"]      = 25;
  $score["invalid"]   = 0;

  // ward against hacking attempts
  if (!isset($score[$rating]))
    ot_handle_error("Problem in update_translation_reliability_percentage() - unknown rating from review form: <b>$rating</b>");

  $translator_points = ot_get_translator_points($translator_id, $origin_language_id, $target_language_id);
  $reliability_percentage = $translator_points["percent_reliability"];
  $num_peer_reviews = $translator_points["num_peer_reviews"];
  $updated_peer_reviews = $num_peer_reviews + 1;
  $new_percentage = (($num_peer_reviews * $reliability_percentage) + $score[$rating]) / $updated_peer_reviews;

  mysql_query("
    UPDATE tr_translator_points
    SET    num_peer_reviews = $updated_peer_reviews,
           percent_reliability = $new_percentage
    WHERE  translator_id = $translator_id AND
           origin_language_id = $origin_language_id AND
           target_language_id = $target_language_id
             ");
}


/**
 * Called after a review is made. This updates the translator's review points.
 */
function ot_update_review_points($translator_id, $origin_language_id, $target_language_id, $data_size)
{
  $translator_points = ot_get_translator_points($translator_id, $origin_language_id, $target_language_id);

  $num_reviews = $translator_points["num_reviews"] + 1;
  $updated_points = ot_calculate_points("review", $data_size) + $translator_points["review_points"];

  mysql_query("
    UPDATE tr_translator_points
    SET    num_reviews = $num_reviews,
           review_points = $updated_points
    WHERE  translator_id = $translator_id AND
           origin_language_id = $origin_language_id AND
           target_language_id = $target_language_id
             ");
}


/**
 * Updates the TOTAL stats for an individual translator, regardless of language:
 *                total # translations
 *                total # reviews
 *                total review points
 *                total translations points
 *
 * This function should be called in a cron at some interval or other. It's really just
 * for administrative purposes.
 */
function ot_update_total_translators_stats($translator_id)
{
  global $LANG;

  $translator_points = ot_get_all_translator_points($translator_id);

  // now tot up the various points
  $total_translations = 0;
  $total_reviews = 0;
  $total_review_points = 0;
  $total_translation_points = 0;

  $total_percentage = 0;
  $num_percentages  = 0;


  foreach ($translator_points as $stats)
  {
    $total_translations  += $stats["num_translations"];
    $total_reviews       += $stats["num_reviews"];
    $total_review_points += $stats["review_points"];
    $total_translation_points += $stats["translation_points"];

    // this sums up the
    $total_percentage    += ($stats["num_peer_reviews"] * $stats["percent_reliability"]);
    $num_percentages     += $stats["num_peer_reviews"];
  }

  // as long
  $total_percent_reliable = ($num_percentages > 0) ? $total_percentage / $num_percentages : 100;


  // update the translator table
  mysql_query("
    UPDATE tr_translators
    SET    total_translations = $total_translations,
           total_reviews = $total_reviews,
           total_review_points = $total_review_points,
           total_percent_reliable = $total_percent_reliable,
           total_translation_points = $total_translation_points
    WHERE  translator_id = $translator_id
      ");

  return array(true, $LANG["text_translator_stats_updated"]);
}


/**
 * Calculates the number of points for a translation or translation review.
 */
function ot_calculate_points($type, $num_words)
{
  global $g_PARAGRAPH_SIZE, $g_SENTENCE_SIZE, $g_PHRASE_SIZE;

	$points = 1;

  if ($type == "translation")
	{
    if ($num_words > $g_PARAGRAPH_SIZE)
      $points = 10 * floor($num_words / 100);  // 10 points every 100 words
    else if ($num_words > $g_SENTENCE_SIZE)
      $points = 8;
    else if ($num_words > $g_PHRASE_SIZE)
      $points = 4;
    else if ($num_words > 1)
      $points = 2;
  }

  if ($type == "review")
	{
    if ($num_words > $g_PARAGRAPH_SIZE)
      $points = 5 * floor($num_words / 100);  // 10 points every 100 words
    else if ($num_words > $g_SENTENCE_SIZE)
      $points = 4;
    else if ($num_words > $g_PHRASE_SIZE)
      $points = 2;
    else if ($num_words > 1)
      $points = 1;
  }

	return $points;
}
