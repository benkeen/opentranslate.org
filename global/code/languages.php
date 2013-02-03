<?php

/*------------------------------------------------------------------------------------------------*\
  Copyright (C) 2010 Benjamin Keen
  http://www.opentranslate.org
\*------------------------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------------------------*\
  LANGUAGES - functions in brief

    ot_get_languages
    ot_get_language_name

\*------------------------------------------------------------------------------------------------*/


/**
 * To retrieve all language information in a hash.
 *
 * @param string $flag - "ui" - returns only complete translations for the UI
 */
function ot_get_languages($flag = "")
{
  $where_clause = "";
  if ($flag == "ui")
    $where_clause = "WHERE ui_version_available = 'yes'";

  $query = mysql_query("
    SELECT *
		FROM   tr_languages
    $where_clause
		ORDER BY language_name
	    ");

	$infohash = array();
	while ($field = mysql_fetch_assoc($query))
    $infohash[] = $field;

	return $infohash;
}

/**
 * Returns all information about a langage.
 *
 * @param integer $language_id
 * @return array
 */
function ot_get_language_info($language_id)
{
  $query = mysql_query("
    SELECT *
		FROM   tr_languages
		WHERE  language_id = $language_id
	    ");

  return mysql_fetch_assoc($query);
}

/**
 * Gets the name of a language based on its ID.
 */
function ot_get_language_name($language_id)
{
  $query = mysql_query("
    SELECT language_name
		FROM   tr_languages
    WHERE  language_id = $language_id
	    ");

  $language_name = "";
	while ($field = mysql_fetch_assoc($query))
    $language_name = $field["language_name"];

	return $language_name;
}
