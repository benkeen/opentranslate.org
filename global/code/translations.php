<?php

/*------------------------------------------------------------------------------------------------*\
  Copyright (C) 2010  Benjamin Keen
  http://www.opentranslate.org
\*------------------------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------------------------*\
  TRANSLATIONS - functions in brief

    ot_delete_translation
    ot_delete_translation_history_entry
    ot_get_data_translation
    ot_get_data_translation_by_translation_id
    ot_get_data_translations
    ot_get_multiple_data_translations
    ot_get_data_translation_history
    ot_get_data_translation_history_entry
    ot_get_data_translation_reviews
    ot_search_data_translation
    ot_make_translation
    ot_make_bulk_translation
    ot_review_translation
    ot_admin_update_translation
    ot_make_bulk_review
    ot_update_translation_history
    ot_set_data_translation_approval_override

\*------------------------------------------------------------------------------------------------*/


/**
 * Deletes a particular translation and its history. We DON'T delete the reviews, though.
 */
function ot_delete_translation($translation_id)
{
  // delete the actual translation and translation history
  mysql_query("DELETE FROM tr_data_translation_reviews WHERE translation_id = $translation_id");
  mysql_query("DELETE FROM tr_data_translation_history WHERE translation_id = $translation_id");
  mysql_query("DELETE FROM tr_data_translations WHERE translation_id = $translation_id");
}


/**
 * Deletes a specific entry in the translation history table; specifically: the LAST translation
 * in the translation history table. This function also updates the appropriate translator stats...
 */
function ot_delete_translation_history_entry($translation_history_id)
{
  $query = mysql_query("
    SELECT d.data_size, dth.account_id, dtr.review
    FROM   tr_data d, tr_data_translations dt, tr_data_translation_history dth, tr_data_translation_reviews dtr
    WHERE  d.data_id = dt.data_id AND
           dt.translation_id = dth.translation_id AND
           dt.translation_id = dtr.translation_id AND
           dtr.translator_id = dth.account_id AND
           dth.translation_history_id = $translation_history_id
      ");
  $result = mysql_fetch_assoc($query);

  // TODO
  echo "in delete_translation_history_entry()";
  print_r($result);
  exit;

  $data_size = $result["data_size"];
  $deleted_entry_translator_id = $result["account_id"];

  exit;

  // delete the history table entry
  mysql_query("DELETE FROM tr_data_translation_history WHERE translation_history_id = $translation_history_id");

  // update the translator's stats
  /*
  num_reviews - 1
  review_points - (data_size * points...)
  */

  // finally, update the PREVIOUS translator's stats: their reliability percentage & their num_peer_reviews
}


/**
 * Retrieves all translations of a piece of data, regardless of language
 */
function ot_get_data_translations($data_id)
{
  $query = mysql_query("
    SELECT *
    FROM   tr_data_translations
    WHERE  data_id = $data_id
      ") or ot_handle_error(mysql_error());

  $infohash = array();
  while ($row = mysql_fetch_assoc($query))
    $infohash[] = $row;

  return $infohash;
}


function ot_get_data_translation($data_id, $language_id)
{
  $query = mysql_query("
    SELECT *
    FROM   tr_data d, tr_data_translations dt
    WHERE  d.data_id = $data_id AND
           d.data_id = dt.data_id AND
           dt.language_id = $language_id
      ") or ot_handle_error(mysql_error());

  return mysql_fetch_assoc($query);
}


/**
 * Retrieves a specific translation by its translation ID.
 */
function ot_get_data_translation_by_translation_id($translation_id)
{
  $query = mysql_query("
    SELECT *
    FROM   tr_data_translations
    WHERE  translation_id = $translation_id
      ") or ot_handle_error(mysql_error());

  $result = mysql_fetch_assoc($query);

  return $result;
}


/**
 * Returns a query of all the data ids specified in the parameter.
 */
function ot_get_multiple_data_translations($data_ids, $language_id)
{
  if (empty($data_ids))
    return array();

  $where_clause_arr = array();
  foreach ($data_ids as $data_id)
    $where_clause_arr[] = "d.data_id = $data_id";

  $where_clause = "(" . join(" OR ", $where_clause_arr) . ")";
	$query = mysql_query("
	  SELECT d.*, dt.translation
    FROM   tr_data d, tr_data_translations dt
    WHERE  d.data_id = dt.data_id AND
           dt.language_id = $language_id AND
		$where_clause
      ") or ot_handle_error(mysql_error());

  return $query;
}


/**
 * Returns the entire translation history for a translation.
 */
function ot_get_data_translation_history($translation_id)
{
  $query = mysql_query("
    SELECT *
    FROM   tr_data_translation_history
    WHERE  translation_id = $translation_id
    ORDER BY change_date ASC
      ");

  $info = array();
  while ($row = mysql_fetch_assoc($query))
    $info[] = $row;

  return $info;
}


/**
 * Returns a specific translation history entry for a translation.
 */
function ot_get_data_translation_history_entry($translation_history_id)
{
  $query = mysql_query("
    SELECT *
    FROM   tr_data_translation_history
    WHERE  translation_history_id = $translation_history_id
      ");

  $info = mysql_fetch_assoc($query);

  return $info;
}


/**
 * Returns all reviews for a translation.
 */
function ot_get_data_translation_reviews($translation_id)
{
  $query = mysql_query("
    SELECT *
    FROM   tr_data_translation_reviews
    WHERE  translation_id = $translation_id
    ORDER BY date_reviewed ASC
      ");

  $info = array();
  while ($row = mysql_fetch_assoc($query))
    $info[] = $row;

  return $info;
}


/**
 *
 * @param $version_id
 * @param $results_per_page
 * @param $page_num
 * @param $order
 * @param $language_id
 * @param $account_id
 * @param $type_filter
 * @param $category_id
 * @param $data_size
 * @param $search_criteria
 * @return unknown_type
 */
function ot_search_data_translation($version_id, $results_per_page, $page_num, $order, $language_id,
    $account_id = "", $type_filter = "", $category_id = "", $data_size = "all", $search_criteria = array())
{
	// find the base version
	$base_version_id = ot_get_base_version($version_id);

	// if we're just searching a base version, sweet! The query is nice and simple.
	$return_hash = array();
	if ($base_version_id == $version_id)
	{
		$return_hash = _ot_search_data_translation_base_version($version_id, $results_per_page, $page_num, $order, $language_id,
      $account_id, $type_filter, $category_id, $data_size, $search_criteria);
	}
	else
	{
    $return_hash = _ot_search_data_translation_child_version($version_id, $results_per_page, $page_num, $order, $language_id,
      $account_id, $type_filter, $category_id, $data_size, $search_criteria);
	}

  return $return_hash;
}


/**
 * Called by translators on their main translation page. Searches and returns information for a particular
 * version, translation language and original data set - as well as category, data size. There's also an optional
 * search that further limits the results.
 *
 * @param integer $version_id
 * @param integer $results_per_page
 * @param integer $page_num
 * @param string $order
 * @param integer $language_id
 * @param integer $account_id
 * @param string $type_filter
 * @param integer $category_id
 * @param string $data_size
 * @param array $search_criteria
 * @return unknown_type
 */
function _ot_search_data_translation_base_version($version_id, $results_per_page, $page_num, $order, $language_id,
    $account_id, $type_filter = "", $category_id = "", $data_size = "all", $search_criteria = array())
{
  global $g_PHRASE_SIZE, $g_SENTENCE_SIZE, $g_PARAGRAPH_SIZE;

  // sorting by column, format: col_x-desc / col_y-asc
  list($column, $direction) = split("-", $order);

  if ($column == "data_category_order")
    $order_by = "pvc.category_order $direction, d.data_category_order $direction ";
  else
    $order_by = "d.$column $direction";

  // if required, return a specific category only
  $extra_where_clause = "";
  if (!empty($category_id) && $category_id != "all")
    $extra_where_clause = "AND d.category_id = $category_id";

  // if required, limit the size
  $size_limit_clause = "";
  switch ($data_size)
  {
    // word
    case "words":
      $size_limit_clause = "AND d.data_size = 1";
      break;

    // phrase
    case "phrases":
      $size_limit_clause = "AND d.data_size > 1 AND d.data_size <= $g_PHRASE_SIZE";
      break;

    // sentence
    case "sentences":
      $size_limit_clause = "AND d.data_size > $g_PHRASE_SIZE AND d.data_size <= $g_SENTENCE_SIZE";
      break;

    // paragraph
    case "paragraphs":
      $size_limit_clause = "AND d.data_size > $g_SENTENCE_SIZE AND d.data_size <= $g_PARAGRAPH_SIZE";
      break;

    // document
    case "documents":
      $size_limit_clause = "AND d.data_size > $g_PARAGRAPH_SIZE";
      break;
  }

  // determine the LIMIT clause
  $limit_clause = "";
  if ($results_per_page != "all")
  {
    if (empty($page_num))
      $page_num = 1;
    $first_item = ($page_num - 1) * $results_per_page;

    $limit_clause = "LIMIT $first_item, $results_per_page";
  }

  // if performing a search, add those particular limitations
  if (!empty($search_criteria))
  {
    if (isset($search_criteria["data_string"]))
      $extra_where_clause .= " AND (d.data LIKE '%{$search_criteria['data_string']}%' OR d.data_label LIKE '%{$search_criteria['data_string']}%')";
  }

  // lastly, customize the particular data set by type: "your translations", "needing translation",
  // "needing review", etc. This phase adds considerable complexity to the resulting SQL query, since it needs
  // to significantly modify the SQL to accommodate the task at hand. So rather than output a single, complicated,
  // hard to maintain query, we build a bunch of different ones
  switch ($type_filter)
  {
    // translator wants to see his OWN translations only
    case "my_translations":
      $full_query = "
          SELECT *, d.data_id as curr_data_id
          FROM   tr_data d
            INNER JOIN tr_project_categories pvc ON d.category_id = pvc.category_id
            INNER JOIN tr_data_translations dt ON d.data_id = dt.data_id AND dt.language_id = $language_id AND dt.translator_id = $account_id
            LEFT JOIN tr_session_locked_data_language sldl ON d.data_id = sldl.data_id AND sldl.language_id = $language_id AND sldl.account_id != $account_id
          WHERE  d.version_id = $version_id AND
                 pvc.export_only = 'no'
                 $extra_where_clause
                 $size_limit_clause
          ORDER BY $order_by
                 $limit_clause
                    ";
      $count_query = "
          SELECT count(*) as num_results
          FROM   tr_data d
            INNER JOIN tr_project_categories pvc ON d.category_id = pvc.category_id
            INNER JOIN tr_data_translations dt ON d.data_id = dt.data_id AND dt.language_id = $language_id AND dt.translator_id = $account_id
          WHERE  d.version_id = $version_id AND
                 pvc.export_only = 'no'
                 $extra_where_clause
                 $size_limit_clause
                     ";
      break;

    // translator wants to see anything that still needs translation
    case "needing_translation":
      $full_query = "
          SELECT *, d.data_id as curr_data_id
          FROM   tr_data d
            INNER JOIN tr_project_categories pvc ON d.category_id = pvc.category_id
            LEFT JOIN tr_session_locked_data_language sldl ON d.data_id = sldl.data_id AND sldl.language_id = $language_id AND sldl.account_id != $account_id
          WHERE  d.version_id = $version_id AND
								 d.data_id NOT IN (SELECT data_id FROM tr_data_translations WHERE language_id = $language_id) AND
                 pvc.export_only = 'no'
                 $extra_where_clause
                 $size_limit_clause
          ORDER BY $order_by
                 $limit_clause
                    ";
      $count_query = "
          SELECT count(*) as num_results
          FROM   tr_data d
            INNER JOIN tr_project_categories pvc ON d.category_id = pvc.category_id
            LEFT JOIN tr_data_translations dt ON d.data_id = dt.data_id AND dt.language_id = $language_id
          WHERE  d.version_id = $version_id AND
								 d.data_id NOT IN (SELECT data_id FROM tr_data_translations WHERE language_id = $language_id) AND
                 pvc.export_only = 'no'
                 $extra_where_clause
                 $size_limit_clause
                     ";
      break;

		// the translator wants to see everything that needs review. Note: this doesn't just return everything "in_review" -
		// it only returns a subset of all "in_review" items: those that the translator didn't (a) translate or (b) already
		// review
    case "needing_review":
      $full_query = "
          SELECT *, d.data_id as curr_data_id
          FROM   tr_data_translations dt, tr_project_categories pvc, tr_data d
            LEFT JOIN tr_session_locked_data_language sldl ON d.data_id = sldl.data_id AND sldl.language_id = $language_id AND sldl.account_id != $account_id
          WHERE  d.data_id = dt.data_id AND
								 d.category_id = pvc.category_id AND
								 d.version_id = $version_id AND
								 dt.language_id = $language_id AND
								 dt.translator_id != $account_id AND
								 dt.translation_status = 'in_review' AND
								 dt.translation_id NOT IN (SELECT translation_id FROM tr_data_translation_reviews WHERE translator_id = $account_id) AND
                 pvc.export_only = 'no'
                 $extra_where_clause
                 $size_limit_clause
          ORDER BY $order_by
                 $limit_clause
                    ";
      $count_query = "
          SELECT count(*) as num_results
          FROM   tr_data_translations dt, tr_project_categories pvc, tr_data d
          WHERE  d.data_id = dt.data_id AND
								 d.version_id = $version_id AND
								 dt.language_id = $language_id AND
								 dt.translator_id != $account_id AND
								 d.category_id = pvc.category_id AND
								 dt.translation_status = 'in_review' AND
								 dt.translation_id NOT IN (SELECT translation_id FROM tr_data_translation_reviews WHERE translator_id = $account_id) AND
                 pvc.export_only = 'no'
                 $extra_where_clause
                 $size_limit_clause
                     ";
      break;

		// this returns anything that the translator can DO something with: either review or translate
		// (this is just the union of the "needing_translation" and "needing_review" queries above.
    case "open":
      $full_query = "
          SELECT *, d.data_id as curr_data_id
          FROM   tr_project_categories pvc, tr_data d
					   LEFT JOIN tr_data_translations dt2 ON dt2.data_id = d.data_id AND dt2.language_id = $language_id
             LEFT JOIN tr_session_locked_data_language sldl ON d.data_id = sldl.data_id AND sldl.language_id = $language_id AND sldl.account_id != $account_id
          WHERE  (d.data_id IN (

                    SELECT d.data_id as data_id
                    FROM   tr_data d
                      INNER JOIN tr_project_categories pvc ON d.category_id = pvc.category_id
                    WHERE  d.version_id = $version_id AND
                           d.data_id NOT IN (SELECT data_id FROM tr_data_translations WHERE language_id = $language_id) AND
                           pvc.export_only = 'no'
                           $extra_where_clause
                           $size_limit_clause

                        ) OR d.data_id IN (

                    SELECT d.data_id as data_id
                    FROM   tr_data_translations dt, tr_project_categories pvc, tr_data d
                    WHERE  d.data_id = dt.data_id AND
                           d.category_id = pvc.category_id AND
                           d.version_id = $version_id AND
                           dt.language_id = $language_id AND
                           dt.translator_id != $account_id AND
                           dt.translation_status = 'in_review' AND
                           dt.translation_id NOT IN (SELECT translation_id FROM tr_data_translation_reviews WHERE translator_id = $account_id) AND
                           pvc.export_only = 'no'
                           $extra_where_clause
                           $size_limit_clause
                        )
                  ) AND
                  d.category_id = pvc.category_id								
          ORDER BY $order_by
                 $limit_clause
      ";

      $count_query = "
			    SELECT count(*) as num_results
					FROM   tr_project_categories pvc, tr_data d
             LEFT JOIN tr_session_locked_data_language sldl ON d.data_id = sldl.data_id AND sldl.language_id = $language_id AND sldl.account_id != $account_id
					WHERE  (d.data_id IN (

                    SELECT d.data_id as data_id
                    FROM   tr_data d
                      INNER JOIN tr_project_categories pvc ON d.category_id = pvc.category_id
                    WHERE  d.version_id = $version_id AND
          								 d.data_id NOT IN (SELECT data_id FROM tr_data_translations WHERE language_id = $language_id) AND
                           pvc.export_only = 'no'
                           $extra_where_clause
                           $size_limit_clause

                        ) OR d.data_id IN (

                    SELECT d.data_id as data_id
                    FROM   tr_data_translations dt, tr_project_categories pvc, tr_data d
                    WHERE  d.data_id = dt.data_id AND
          								 d.category_id = pvc.category_id AND
          								 d.version_id = $version_id AND
          								 dt.language_id = $language_id AND
          								 dt.translator_id != $account_id AND
          								 dt.translation_status = 'in_review' AND
          								 dt.translation_id NOT IN (SELECT translation_id FROM tr_data_translation_reviews WHERE translator_id = $account_id) AND
                           pvc.export_only = 'no'
                           $extra_where_clause
                           $size_limit_clause

                        )
                  ) AND
                  d.category_id = pvc.category_id
                     ";
      break;

		// returns any translations marked as completed
    case "completed":
      $full_query = "
          SELECT *, d.data_id as curr_data_id
          FROM   tr_data d
            INNER JOIN tr_project_categories pvc ON d.category_id = pvc.category_id
            INNER JOIN tr_data_translations dt ON d.data_id = dt.data_id AND dt.language_id = $language_id AND dt.translation_status = 'completed'
          WHERE  d.version_id = $version_id AND
                 pvc.export_only = 'no'
                 $extra_where_clause
                 $size_limit_clause
          ORDER BY $order_by
                 $limit_clause
                    ";
      $count_query = "
          SELECT count(*) as num_results
          FROM   tr_data d
            INNER JOIN tr_project_categories pvc ON d.category_id = pvc.category_id
            INNER JOIN tr_data_translations dt ON d.data_id = dt.data_id AND dt.language_id = $language_id AND dt.translation_status = 'completed'
          WHERE  d.version_id = $version_id AND
                 pvc.export_only = 'no'
                 $extra_where_clause
                 $size_limit_clause
                     ";
      break;


    // the default query: returns everything for a version
    default:
      $full_query = "
          SELECT *, d.data_id as curr_data_id
          FROM   tr_data d
            INNER JOIN tr_project_categories pvc ON d.category_id = pvc.category_id
            LEFT JOIN tr_data_translations dt ON d.data_id = dt.data_id AND dt.language_id = $language_id
            LEFT JOIN tr_session_locked_data_language sldl ON d.data_id = sldl.data_id AND sldl.language_id = $language_id AND sldl.account_id != $account_id
          WHERE  d.version_id = $version_id AND
                 pvc.export_only = 'no'
                 $extra_where_clause
                 $size_limit_clause
          ORDER BY $order_by
                 $limit_clause
                    ";

      $count_query = "
          SELECT count(*) as num_results
          FROM   tr_data d
            INNER JOIN tr_project_categories pvc ON d.category_id = pvc.category_id
            LEFT JOIN tr_data_translations dt ON d.data_id = dt.data_id AND dt.language_id = $language_id
          WHERE  d.version_id = $version_id AND
                 pvc.export_only = 'no'
                 $extra_where_clause
                 $size_limit_clause
                     ";
  }

  $search_query = mysql_query($full_query) or ot_handle_error(mysql_error());
  $query = mysql_query($count_query) or ot_handle_error(mysql_error());
  $result = mysql_fetch_assoc($query);
  $num_results = $result["num_results"];

  $return_hash["search_query"] = $search_query;
  $return_hash["num_results"]  = $num_results;

  return $return_hash;
}


/**
 * Called by translators on their main translation page. Searches and returns information for a particular
 * version, translation language and original data set - as well as category, data size. There's also an optional
 * search that further limits the results.
 *
 * @param integer $version_id
 * @param integer $results_per_page
 * @param integer $page_num
 * @param string $order
 * @param integer $language_id
 * @param mixed $account_id
 * @param string $type_filter
 * @param integer $category_id
 * @param string $data_size
 * @param array $search_criteria
 * @return array
 */
function _ot_search_data_translation_child_version($version_id, $results_per_page, $page_num, $order, $language_id,
    $account_id, $type_filter = "", $category_id = "", $data_size = "all", $search_criteria = array())
{
  global $g_PHRASE_SIZE, $g_SENTENCE_SIZE, $g_PARAGRAPH_SIZE;

  // sorting by column, format: col_x-desc / col_y-asc
  list($column, $direction) = split("-", $order);

  if ($column == "data_category_order")
    $order_by = "pvc.category_order $direction, d.data_category_order $direction ";
  else
    $order_by = "d.$column $direction";

  // if required, return a specific category only
  $extra_where_clause = "";
  if (!empty($category_id) && $category_id != "all")
    $extra_where_clause = "AND d.category_id = $category_id";

  // if required, limit the size
  $size_limit_clause = "";
  switch ($data_size)
  {
    // word
    case "words":
      $size_limit_clause = "AND d.data_size = 1";
      break;

    // phrase
    case "phrases":
      $size_limit_clause = "AND d.data_size > 1 AND d.data_size <= $g_PHRASE_SIZE";
      break;

    // sentence
    case "sentences":
      $size_limit_clause = "AND d.data_size > $g_PHRASE_SIZE AND d.data_size <= $g_SENTENCE_SIZE";
      break;

    // paragraph
    case "paragraphs":
      $size_limit_clause = "AND d.data_size > $g_SENTENCE_SIZE AND d.data_size <= $g_PARAGRAPH_SIZE";
      break;

    // document
    case "documents":
      $size_limit_clause = "AND d.data_size > $g_PARAGRAPH_SIZE";
      break;
  }

  // determine the LIMIT clause
  $limit_clause = "";
  if ($results_per_page != "all")
  {
    if (empty($page_num))
      $page_num = 1;
    $first_item = ($page_num - 1) * $results_per_page;

    $limit_clause = "LIMIT $first_item, $results_per_page";
  }

  // if performing a search, add those particular limitations
  if (!empty($search_criteria))
  {
    if (isset($search_criteria["data_string"]))
      $extra_where_clause .= " AND (d.data LIKE '%{$search_criteria['data_string']}%' OR d.data_label LIKE '%{$search_criteria['data_string']}%')";
  }


  $version_chain = ot_get_parent_versions($version_id);
  $version_chain_str = join(", ", $version_chain);

  array_shift($version_chain);  // remove the base version
  $version_chain_str_omit_base_version = join(", ", $version_chain);


  // lastly, customize the particular data set by type: "your translations", "needing translation",
  // "needing review", etc. This phase adds considerable complexity to the resulting SQL query, since it needs
  // to significantly modify the SQL to accommodate the task at hand. So rather than output a single, complicated,
  // hard to maintain query, we build a bunch of different ones
  switch ($type_filter)
  {
    // translator wants to see his OWN translations only
    case "my_translations":
      $full_query = "
          SELECT *, d.data_id as curr_data_id
          FROM   tr_data d
            INNER JOIN tr_project_categories pvc ON d.category_id = pvc.category_id
            INNER JOIN tr_data_translations dt ON d.data_id = dt.data_id AND dt.language_id = $language_id AND dt.translator_id = $account_id
            LEFT JOIN tr_session_locked_data_language sldl ON d.data_id = sldl.data_id AND sldl.language_id = $language_id AND sldl.account_id != $account_id
				  WHERE  d.version_id IN ($version_chain_str) AND
                 pvc.export_only = 'no' AND
				  			 d.data_id NOT IN (SELECT data_id FROM tr_data_version_changes WHERE version_id IN ($version_chain_str_omit_base_version)) AND
				  			 d.data_id NOT IN (SELECT data_id FROM tr_data_version_deletions WHERE version_id IN ($version_chain_str_omit_base_version))							 
                 $extra_where_clause
                 $size_limit_clause
          ORDER BY $order_by
                 $limit_clause
                    ";
      $count_query = "
          SELECT count(*) as num_results
          FROM   tr_data d
            INNER JOIN tr_project_categories pvc ON d.category_id = pvc.category_id
            INNER JOIN tr_data_translations dt ON d.data_id = dt.data_id AND dt.language_id = $language_id AND dt.translator_id = $account_id
				  WHERE  d.version_id IN ($version_chain_str) AND
                 pvc.export_only = 'no' AND
				  			 d.data_id NOT IN (SELECT data_id FROM tr_data_version_changes WHERE version_id IN ($version_chain_str_omit_base_version)) AND
				  			 d.data_id NOT IN (SELECT data_id FROM tr_data_version_deletions WHERE version_id IN ($version_chain_str_omit_base_version))
                 $extra_where_clause
                 $size_limit_clause
                     ";
      break;

    // translator wants to see anything that still needs translation
    case "needing_translation":
      $full_query = "
          SELECT *, d.data_id as curr_data_id
          FROM   tr_data d
            INNER JOIN tr_project_categories pvc ON d.category_id = pvc.category_id
            LEFT JOIN tr_session_locked_data_language sldl ON d.data_id = sldl.data_id AND sldl.language_id = $language_id AND sldl.account_id != $account_id
				  WHERE  d.version_id IN ($version_chain_str) AND
								 d.data_id NOT IN (SELECT data_id FROM tr_data_translations WHERE language_id = $language_id) AND
                 pvc.export_only = 'no' AND
				  			 d.data_id NOT IN (SELECT data_id FROM tr_data_version_changes WHERE version_id IN ($version_chain_str_omit_base_version)) AND
				  			 d.data_id NOT IN (SELECT data_id FROM tr_data_version_deletions WHERE version_id IN ($version_chain_str_omit_base_version))								 
                 $extra_where_clause
                 $size_limit_clause
          ORDER BY $order_by
                 $limit_clause
                    ";
      $count_query = "
          SELECT count(*) as num_results
          FROM   tr_data d
            INNER JOIN tr_project_categories pvc ON d.category_id = pvc.category_id
            LEFT JOIN tr_data_translations dt ON d.data_id = dt.data_id AND dt.language_id = $language_id
				  WHERE  d.version_id IN ($version_chain_str) AND
								 d.data_id NOT IN (SELECT data_id FROM tr_data_translations WHERE language_id = $language_id) AND
                 pvc.export_only = 'no' AND
				  			 d.data_id NOT IN (SELECT data_id FROM tr_data_version_changes WHERE version_id IN ($version_chain_str_omit_base_version)) AND
				  			 d.data_id NOT IN (SELECT data_id FROM tr_data_version_deletions WHERE version_id IN ($version_chain_str_omit_base_version))
                 $extra_where_clause
                 $size_limit_clause
                     ";
      break;

		// the translator wants to see everything that needs review. Note: this doesn't just return everything "in_review" -
		// it only returns a subset of all "in_review" items: those that the translator didn't (a) translate or (b) already
		// review
    case "needing_review":
      $full_query = "
          SELECT *, d.data_id as curr_data_id
          FROM   tr_data_translations dt, tr_project_categories pvc, tr_data d
            LEFT JOIN tr_session_locked_data_language sldl ON d.data_id = sldl.data_id AND sldl.language_id = $language_id AND sldl.account_id != $account_id
				  WHERE  d.version_id IN ($version_chain_str) AND
                 d.data_id = dt.data_id AND
								 d.category_id = pvc.category_id AND
								 dt.language_id = $language_id AND
								 dt.translator_id != $account_id AND
								 dt.translation_status = 'in_review' AND
								 dt.translation_id NOT IN (SELECT translation_id FROM tr_data_translation_reviews WHERE translator_id = $account_id) AND
                 pvc.export_only = 'no'
                 $extra_where_clause
                 $size_limit_clause
          ORDER BY $order_by
                 $limit_clause
                    ";
      $count_query = "
          SELECT count(*) as num_results
          FROM   tr_data_translations dt, tr_project_categories pvc, tr_data d
				  WHERE  d.version_id IN ($version_chain_str) AND
					       d.data_id = dt.data_id AND
								 dt.language_id = $language_id AND
								 dt.translator_id != $account_id AND
								 d.category_id = pvc.category_id AND
								 dt.translation_status = 'in_review' AND
								 dt.translation_id NOT IN (SELECT translation_id FROM tr_data_translation_reviews WHERE translator_id = $account_id) AND
                 pvc.export_only = 'no'
                 $extra_where_clause
                 $size_limit_clause
                     ";
      break;

		// this returns anything that the translator can DO something with: either review or translate
		// (this is just the union of the "needing_translation" and "needing_review" queries above.
    case "open":
      $full_query = "
                SELECT *, d.data_id as curr_data_id
                FROM   tr_project_categories pvc, tr_data d
					        LEFT JOIN tr_data_translations dt2 ON dt2.data_id = d.data_id AND dt2.language_id = $language_id								
                  LEFT JOIN tr_session_locked_data_language sldl ON d.data_id = sldl.data_id AND sldl.language_id = $language_id AND sldl.account_id != $account_id
                WHERE  (d.data_id IN (

                    SELECT d.data_id as data_id
                    FROM   tr_data d
                      INNER JOIN tr_project_categories pvc ON d.category_id = pvc.category_id
 	           			  WHERE  d.version_id IN ($version_chain_str) AND
          								 d.data_id NOT IN (SELECT data_id FROM tr_data_translations WHERE language_id = $language_id) AND
                           pvc.export_only = 'no'
                           $extra_where_clause
                           $size_limit_clause

                        ) OR d.data_id IN (

                    SELECT d.data_id as data_id
                    FROM   tr_data_translations dt, tr_project_categories pvc, tr_data d
										WHERE  d.version_id IN ($version_chain_str) AND
                           d.data_id = dt.data_id AND
          								 d.category_id = pvc.category_id AND
          								 dt.language_id = $language_id AND
          								 dt.translator_id != $account_id AND
          								 dt.translation_status = 'in_review' AND
          								 dt.translation_id NOT IN (SELECT translation_id FROM tr_data_translation_reviews WHERE translator_id = $account_id) AND
                           pvc.export_only = 'no'
                           $extra_where_clause
                           $size_limit_clause
                        )
                  ) AND
                  d.category_id = pvc.category_id
          ORDER BY $order_by
                 $limit_clause
      ";

      $count_query = "
			    SELECT count(*) as num_results
					FROM   tr_project_categories pvc, tr_data d
             LEFT JOIN tr_session_locked_data_language sldl ON d.data_id = sldl.data_id AND sldl.language_id = $language_id AND sldl.account_id != $account_id
					WHERE  (d.data_id IN (

                    SELECT d.data_id as data_id
                    FROM   tr_data d
                      INNER JOIN tr_project_categories pvc ON d.category_id = pvc.category_id
                    WHERE  d.version_id = $version_id AND
          								 d.data_id NOT IN (SELECT data_id FROM tr_data_translations WHERE language_id = $language_id) AND
                           pvc.export_only = 'no'
                           $extra_where_clause
                           $size_limit_clause

                        ) OR d.data_id IN (

                    SELECT d.data_id as data_id
                    FROM   tr_data_translations dt, tr_project_categories pvc, tr_data d
                    WHERE  d.data_id = dt.data_id AND
          								 d.category_id = pvc.category_id AND
          								 d.version_id = $version_id AND
          								 dt.language_id = $language_id AND
          								 dt.translator_id != $account_id AND
          								 dt.translation_status = 'in_review' AND
          								 dt.translation_id NOT IN (SELECT translation_id FROM tr_data_translation_reviews WHERE translator_id = $account_id) AND
                           pvc.export_only = 'no'
                           $extra_where_clause
                           $size_limit_clause

                        )
                  ) AND
                  d.category_id = pvc.category_id
                     ";
      break;

		// returns any translations marked as completed
    case "completed":
      $full_query = "
          SELECT *, d.data_id as curr_data_id
          FROM   tr_data d
            INNER JOIN tr_project_categories pvc ON d.category_id = pvc.category_id
            INNER JOIN tr_data_translations dt ON d.data_id = dt.data_id AND dt.language_id = $language_id AND dt.translation_status = 'completed'
          WHERE  d.version_id IN ($version_chain_str) AND
                 pvc.export_only = 'no'
                 $extra_where_clause
                 $size_limit_clause
          ORDER BY $order_by
                 $limit_clause
                    ";
      $count_query = "
          SELECT count(*) as num_results
          FROM   tr_data d
            INNER JOIN tr_project_categories pvc ON d.category_id = pvc.category_id
            INNER JOIN tr_data_translations dt ON d.data_id = dt.data_id AND dt.language_id = $language_id AND dt.translation_status = 'completed'
          WHERE  d.version_id IN ($version_chain_str) AND
                 pvc.export_only = 'no'
                 $extra_where_clause
                 $size_limit_clause
                     ";
      break;


    // the default query: returns everything for a version
    default:
      $full_query = "
          SELECT *, d.data_id as curr_data_id
          FROM   tr_data d
            INNER JOIN tr_project_categories pvc ON d.category_id = pvc.category_id
            LEFT JOIN tr_data_translations dt ON d.data_id = dt.data_id AND dt.language_id = $language_id
            LEFT JOIN tr_session_locked_data_language sldl ON d.data_id = sldl.data_id AND sldl.language_id = $language_id AND sldl.account_id != $account_id
				  WHERE  d.version_id IN ($version_chain_str) AND
				         pvc.export_only = 'no' AND
				  			 d.data_id NOT IN (SELECT data_id FROM tr_data_version_changes WHERE version_id IN ($version_chain_str_omit_base_version)) AND
				  			 d.data_id NOT IN (SELECT data_id FROM tr_data_version_deletions WHERE version_id IN ($version_chain_str_omit_base_version))
				  			 $extra_where_clause
				  			 $limit_clause
                    ";

      $count_query = "
          SELECT count(*) as num_results
          FROM   tr_data d
            INNER JOIN tr_project_categories pvc ON d.category_id = pvc.category_id
            LEFT JOIN tr_data_translations dt ON d.data_id = dt.data_id AND dt.language_id = $language_id
            LEFT JOIN tr_session_locked_data_language sldl ON d.data_id = sldl.data_id AND sldl.language_id = $language_id AND sldl.account_id != $account_id
          WHERE  d.version_id IN ($version_chain_str) AND
				         pvc.export_only = 'no' AND
				  			 d.data_id NOT IN (SELECT data_id FROM tr_data_version_changes WHERE version_id IN ($version_chain_str_omit_base_version)) AND
				  			 d.data_id NOT IN (SELECT data_id FROM tr_data_version_deletions WHERE version_id IN ($version_chain_str_omit_base_version))
                 $extra_where_clause
                 $size_limit_clause
                     ";
  }

  $search_query = mysql_query($full_query) or die(mysql_error());
  $query = mysql_query($count_query) or die(mysql_error());
  $result = mysql_fetch_assoc($query);
  $num_results = $result["num_results"];

  $return_hash["search_query"] = $search_query;
  $return_hash["num_results"]  = $num_results;

  return $return_hash;
}


/**
 * Adds a new translation to the database.
 */
function ot_make_translation($data_id, $language_id, $translator_id, $infohash)
{
  global $LANG;
	
	// add / update
	$action = (isset($infohash["action"])) ? $infohash["action"] : "add"; 

	if ($action == "add")
	{
    // first, check a translation hasn't already been made
    $query = mysql_query("
      SELECT count(*) as c
      FROM   tr_data_translations
      WHERE  data_id = $data_id AND
             language_id = $language_id
               ");
    $result = mysql_fetch_assoc($query);
    if ($result["c"] > 0)
      return array(false, $LANG["validation_translation_already_made"]);
  
    // looks good! Log the new translation and return the translation_id
    $version_id = ot_get_data_version($data_id);
    $translation        = mysql_real_escape_string($infohash["translation"]);
    $origin_language_id = $infohash["origin_language_id"];
    $data_size          = $infohash["data_size"];
    $percent_translated = 100; // this is for version 1
    $now = ot_get_current_datetime();
  
    // if the translation is empty, just return an error message
    $trimmed_str = trim($translation);
    if (empty($trimmed_str))
      return array(false, $LANG["validation_no_translation"]);

    $query = mysql_query("
      INSERT INTO tr_data_translations (data_id, translator_id, last_reviewer_id, language_id, translation,
        percent_translated, creation_date, translation_status, review_count)
      VALUES ($data_id, $translator_id, $translator_id, $language_id, '$translation', $percent_translated, '$now',
        'in_review', 0)
          ") or ot_handle_error(mysql_error());
    $translation_id = mysql_insert_id();

  	// update the last change date for this version-language
    mysql_query("
      UPDATE tr_project_version_language_stats
    	SET    translation_last_change_date = '$now'
    	WHERE  version_id = $version_id AND
    	       language_id = $language_id
    				   ");
  
    // update the translation history table
    ot_update_translation_history($translation_id, $translator_id, $translation, "new");
	}
	else
	{
	  $translation_id = $infohash["translation_id"];
    $version_id = ot_get_data_version($data_id);
    $translation        = mysql_real_escape_string($infohash["translation"]);
    $origin_language_id = $infohash["origin_language_id"];
    $data_size          = $infohash["data_size"];
    $percent_translated = 100; // this is for version 1
    $now = ot_get_current_datetime();

    // if the translation is empty, just return an error message
    $trimmed_str = trim($translation);
    if (empty($trimmed_str))
      return array(false, $LANG["validation_no_translation"]);

    $query = mysql_query("
      UPDATE tr_data_translations 
			SET    translator_id = $translator_id,
			       last_reviewer_id  = $translator_id,
						 translation = '$translation',
             percent_translated = $percent_translated,
						 review_count = 0
      WHERE  translation_id = $translation_id
          ") or ot_handle_error(mysql_error());
					
    // update the translation history table
    ot_update_translation_history($translation_id, $translator_id, $translation, "edit");	
	}

  // update translation points
  ot_update_translation_points($translator_id, $origin_language_id, $language_id, $data_size);

  // unlock this translation
  ot_unlock_translation_language($data_id, $language_id);

  return array(true, $translation_id);
}


/**
 * Called by a translator updating their own translation. This is permitted up until the point
 * where another translator has reviewed the translation, then it is locked for reviews only.
 */
function ot_update_translation($data_id, $translation_id, $translator_id, $language_id, $translation)
{
  global $LANG;

  $version_id = ot_get_data_version($data_id);

  $translation = mysql_real_escape_string($translation);
  $now = ot_get_current_datetime();

  // if the translation is empty, just return an error message
  $trimmed_str = trim($translation);
  if (empty($trimmed_str))
    return array(false, $LANG["validation_no_translation"]);

  // update the translation
  $query = mysql_query("
    UPDATE tr_data_translations
    SET    translation = '$translation',
		       translation_last_change_date = '$now'
    WHERE  translation_id = $translation_id
      ");

	// update the last change date for this version-language
  mysql_query("
    UPDATE tr_project_version_language_stats
  	SET    translation_last_change_date = '$now'
  	WHERE  version_id = $version_id AND
  	       language_id = $language_id
  				   ");

  // update the translation history table
  ot_update_translation_history($translation_id, $translator_id, $translation, "edit");

  // unlock this translation
  ot_unlock_translation_language($data_id, $language_id);

  return array(true, "The translation has been updated.");
}


/**
 * Called on the bulk translate page; adds multiple translations to the database at once. This
 * function calls make_translation, which does the nitty gritty of unlocking the translations,
 * incrementing the translator points, etc.
 */
function ot_make_bulk_translation($language_id, $translator_id, $infohash)
{
  // loop through all data ids and build the translation for
  $data_ids = split(",", $infohash["data_ids"]);

  $result_successes = array();
  $result_messages = array();
  foreach ($data_ids as $data_id)
  {
    $translation_info = array();
    $translation_info["data_id"]     = $data_id;
    $translation_info["translation"] = $infohash["translation_$data_id"];
    $translation_info["origin_language_id"] = $infohash["origin_language_id"];
    $translation_info["data_size"]   = $infohash["data_size_$data_id"];
		
		// added in Sept 2011
    $translation_info["action"] = $infohash["data_action_$data_id"]; // add / edit
		if ($translation_info["action"] == "update")
		{
		  $translation_info["translation_id"] = $infohash["data_translation_id_$data_id"];
		}		

    // if no translation has been supplied for this row, just ignore it
    if (empty($translation_info["translation"]))
      continue;

    list ($success, $message) = ot_make_translation($data_id, $language_id, $translator_id, $translation_info);
    $result_successes[] = $success;
    $result_messages[]  = $message;
  }
	
  return array($result_successes, $result_messages);
}


/**
 * Reviews an existing translation.
 * Returns:  array: [0] T/F
 *                  [1] message (if error);
 *
 * Notes: remind me again, Ben, why this function doesn't use the primary key for tr_data_translations:
 * translation_id??
 */
function ot_review_translation($data_id, $origin_language_id, $target_language_id, $reviewer_id, $trust_threshold, $data_size, $infohash)
{
  global $LANG;

  // retrieve various information about the translation we're about to update. This information
  // is used to update the PREVIOUS translator / reviewers points and reliability
  $infohash = ot_clean_hash($infohash);
  $translation_info = ot_get_data_translation($data_id, $target_language_id);
  $last_reviewer_id = $translation_info["last_reviewer_id"];
	$version_id       = $translation_info["version_id"];
  $data_size = $translation_info["data_size"];
  $review_count = $translation_info["review_count"];
  $translation_id = $translation_info["translation_id"];
  $rating = $infohash["rating"];
  $new_translation = $infohash["new_translation"];


  // first, check that this translator has not already reviewed this item. If they
	// have, just return a message indicating as such
  $check_query = mysql_query("
    SELECT count(*) as num_records
    FROM   tr_data_translations dt, tr_data_translation_reviews dtr
    WHERE  dt.translation_id = dtr.translation_id AND
           dt.data_id = $data_id AND
           dt.language_id = $target_language_id AND
           dtr.translator_id = $reviewer_id
             ");

  $result = mysql_fetch_assoc($check_query);
  if ($result["num_records"] > 0)
    return array(false, $LANG["validation_review_already_made"]);


  // we're good. Now log the new review
  $datetime = ot_get_current_datetime();
  mysql_query("
    INSERT INTO tr_data_translation_reviews (translation_id, translator_id, review, date_reviewed)
    VALUES ($translation_id, $reviewer_id, '$rating', '$datetime')
      ") or ot_handle_error(mysql_error());


  $blacklist_last_translator = false;
  $translation_changed = false;
  $change_type = "";
	$translation_last_change_date = $translation_info["translation_last_change_date"];
  switch ($rating)
  {
    case "excellent":
      $review_count++;
      $translation_status = "in_review";
      if ($review_count >= $trust_threshold)
        $translation_status = "complete";
      break;

    case "invalid":
      $blacklist_last_translator = false;
      $change_type = "invalid";
      $translation_changed = true;
      $review_count = 0;
      $translation_status = "in_review";
      break;

    // otherwise, the translation has been changed. Reset the review count
    default:
      $translation_changed = true;
      $change_type = "edit";
      $review_count = 0;
      $translation_status = "in_review";
			$translation_last_change_date = ot_get_current_datetime();
      break;
  }

  // regardless of the review, SOMETHING will have changed. Update the data_translation table.
  mysql_query("
    UPDATE tr_data_translations
    SET    translation = '$new_translation',
           translation_status = '$translation_status',
           review_count = $review_count,
           last_reviewer_id = $reviewer_id,
					 translation_last_change_date = '$translation_last_change_date'
    WHERE  translation_id = $translation_id
      ") or ot_handle_error(mysql_error());

  // if the translation was changed, add this new translation to the history table and update
	// the last translation date in the version-language table
  if ($translation_changed)
	{
    ot_update_translation_history($translation_id, $reviewer_id, $new_translation, $change_type);

		mysql_query("
		  UPDATE tr_project_version_language_stats
			SET    translation_last_change_date = '$translation_last_change_date'
			WHERE  version_id = $version_id AND
			       language_id = $target_language_id
						   ");
  }


  // to think about!
  if ($blacklist_last_translator)
  {

  }

  // update the PREVIOUS translator's reliability percentage (this may be a reviewer or a translator;
  // it doesn't matter)
  ot_update_translation_reliability_percentage($last_reviewer_id, $origin_language_id, $target_language_id, $rating);

  // update this translator's review points
  ot_update_review_points($reviewer_id, $origin_language_id, $target_language_id, $data_size);

  return array(true, "");
}


/**
 * Called by administrator only [will be expanded to project managers]. This doesn't leave any trace;
 * it simply updates the translation - nothing else.
 */
function ot_admin_update_translation($translation_id, $translation)
{
  $translation = mysql_real_escape_string($translation);
  mysql_query("
    UPDATE tr_data_translations
    SET    translation = '$translation'
    WHERE  translation_id = $translation_id
      ");

  return array(true, "The translation has been updated.");
}


/**
 * Called on the bulk review page; lets the translator review multiple translations in a single query.
 */
function ot_make_bulk_review($origin_language_id, $target_language_id, $reviewer_id, $trust_threshold, $infohash)
{
  $infohash = ot_clean_hash($infohash);

  $data_ids = split(",", $infohash["data_ids"]);

  $result_successes = array();
  $result_messages = array();
  foreach ($data_ids as $data_id)
  {
    $data_size = $infohash["data_size_$data_id"];

    $hash = array();
    $hash["rating"] = isset($infohash["rating_$data_id"]) ? $infohash["rating_$data_id"] : "";
    $hash["new_translation"] = $infohash["new_translation_$data_id"];

    // if no rating has been given for this row, just ignore it
    if (empty($hash["rating"]))
      continue;

    list ($success, $message) = ot_review_translation($data_id, $origin_language_id, $target_language_id, $reviewer_id, $trust_threshold, $data_size, $hash);
    $result_successes[] = $success;
    $result_messages[]  = $message;
  }

  return array($result_successes, $result_messages);
}


/**
 * Updates the translation history table. This is called whenever a translation is changed
 * or added. It logs each and every change to a translation so the project manager / administrator
 * can review the sequence of changes if they want to.
 */
function ot_update_translation_history($translation_id, $account_id, $translation, $reason)
{
  $now = ot_get_current_datetime();
  mysql_query("
    INSERT INTO tr_data_translation_history (translation_id, account_id, translation, reason_for_change, change_date)
    VALUES ($translation_id, $account_id, '$translation', '$reason', '$now')
    ") or ot_handle_error(mysql_error());
}


/**
 * Called by administrators and project managers only. This lets them "approve" a translation (i.e. mark
 * it as complete) and override the need for further reviewal.
 */
function ot_set_data_translation_approval_override($translation_id, $translator_id, $trust_threshold)
{
  global $LANG;

  $now = ot_get_current_datetime();
  mysql_query("
    UPDATE tr_data_translations
    SET    review_count = $trust_threshold,
           translation_status = 'completed',
           approval_override_account_id = $translator_id,
           approval_override_date = '$now'
    WHERE  translation_id = $translation_id
      ");

  return array(true, $LANG["text_translation_approve_override"]);
}


/**
 * Returns the total number of translations made for a particular version-language.
 *
 * @param integer $version_id
 * @param integer $language_id
 * @return integer
 */
function ot_get_num_version_data_translations($version_id, $language_id, $omit_export_only = true)
{
	$base_version_id = ot_get_base_version($version_id);

	$export_only_clause = ($omit_export_only) ? "AND pc.export_only = 'no'" : "";

	if ($base_version_id == $version_id)
	{
	  $count_query = mysql_query("
	    SELECT count(*) as num_translations
	    FROM   tr_data_translations dt, tr_data d, tr_project_categories pc
	    WHERE  dt.data_id = d.data_id AND
	           d.category_id = pc.category_id AND
	           d.version_id = $version_id AND
	           language_id = $language_id
	           $export_only_clause
	             ") or die(mysql_error());
	}
	else
	{
	  $version_chain = ot_get_parent_versions($version_id);
	  $version_chain_str = join(", ", $version_chain);

	  array_shift($version_chain);  // remove the base version
	  $version_chain_str_omit_base_version = join(", ", $version_chain);

    $count_query = mysql_query("
		  SELECT count(*) as num_translations
		  FROM   tr_data_translations dt, tr_data d, tr_project_categories pc
		  WHERE  d.version_id IN ($version_chain_str) AND
		         d.category_id = pc.category_id AND
		         d.data_id = dt.data_id AND
		         language_id = $language_id AND
		  			 d.data_id NOT IN (SELECT data_id FROM tr_data_version_changes WHERE version_id IN ($version_chain_str_omit_base_version)) AND
		  			 d.data_id NOT IN (SELECT data_id FROM tr_data_version_deletions WHERE version_id IN ($version_chain_str_omit_base_version))
		  			 $export_only_clause
        ");
	}

  $result = mysql_fetch_assoc($count_query);

  return $result["num_translations"];
}


/**
 * Uses Google Translate to blindly translate an arbitrary chunk of available data in a version-language.
 *
 * @param integer $version_id
 * @param integer $language_id
 * @param integer $num_items
 * @return array [0] the number of translated items, [1] the remaining number of data to translate
 */
function ot_auto_translate($version_id, $language_id, $num_items)
{
	global $g_google_translator_id;

  // find out how many items are available to translate
  $total_num_data_items = ot_get_num_version_data($version_id);

  // find out how many items are already translate
  $num_translations = ot_get_num_version_data_translations($version_id, $language_id);

  // alright! So how many items are left to translate?
  $num_untranslated_items = $total_num_data_items - $num_translations;

  // find the next N items that haven't been translated
  $actual_num_items_to_translate = ($num_items > $num_untranslated_items) ? $num_untranslated_items : $num_items;
	
	if ($actual_num_items_to_translate < 0) 
	  $actual_num_items_to_translate = 0;

  $results = ot_search_data_translation($version_id, $actual_num_items_to_translate, 1, "data_category_order-ASC",
    $language_id, $g_google_translator_id, "needing_translation");

  // find the google language codes for the source and target language
  $project_info = ot_get_project_from_version_id($version_id);
  $origin_language_id = $project_info["origin_language_id"];
  $origin_language_info = ot_get_language_info($origin_language_id);
  $google_translate_source_language_code = $origin_language_info["google_translate_code"];

  $target_language_info = ot_get_language_info($language_id);
  $google_translate_target_language_code = $target_language_info["google_translate_code"];

  // now translate 'em! Locks are made by the ot_make_translation function 
  $num_new_translations = 0;
  $gt = new Gtranslate;
	$success = true;
	$error = "";
  while ($info = mysql_fetch_assoc($results["search_query"]))
  {
    $data_id   = $info["curr_data_id"];
    $data      = $info["data"];
    $data_size = $info["data_size"];

    try
    {
      // get the Google translation
      $translation = $gt->query(array($google_translate_source_language_code, $google_translate_target_language_code), $data);
//			print($translation);
//			exit;
			
      // make the translation of this item in the database
      $infohash = array(
        "translation"        => mysql_real_escape_string($translation),
        "origin_language_id" => $origin_language_id,
        "data_size"          => $data_size
      );

      list($success, $translation_id) = ot_make_translation($data_id, $language_id, $g_google_translator_id, $infohash);

      if ($success)
        $num_new_translations++;
			else
			{
			  $success = false;
				$error = $translation_id;
			  break;
			}
    }
    catch (GTranslateException $ge)
    {
		  $success = false;
      $error = mysql_real_escape_string($ge->getMessage());
			break;
    }
  }

  $remaining_items = $num_untranslated_items - $num_new_translations;
  
  return array($success, $error, $num_new_translations, $remaining_items);
}


/**
 * This figures out whether a particular version-language contains 1 or more unreviewed
 * auto-translations made by Google Translate.
 */
function ot_get_unreviewed_auto_translations($version_id, $language_id)
{
  global $g_google_translator_id;
	
	$base_version_id = ot_get_base_version($version_id);

	if ($base_version_id == $version_id)
	{
	  $count_query = mysql_query("
	    SELECT count(*) as c
	    FROM   tr_data_translations dt, tr_data d
	    WHERE  dt.data_id = d.data_id AND
	           d.version_id = $version_id AND
	           language_id = $language_id AND
						 dt.translator_id = $g_google_translator_id
	             ") or die(mysql_error());
	}
	else
	{
	  $version_chain = ot_get_parent_versions($version_id);
	  $version_chain_str = join(", ", $version_chain);

	  array_shift($version_chain);  // remove the base version
	  $version_chain_str_omit_base_version = join(", ", $version_chain);

    $count_query = mysql_query("
		  SELECT count(*) as c
		  FROM   tr_data_translations dt, tr_data d
		  WHERE  d.version_id IN ($version_chain_str) AND
		         d.data_id = dt.data_id AND
		         language_id = $language_id AND
						 dt.translator_id = $g_google_translator_id AND						 
		  			 d.data_id NOT IN (SELECT data_id FROM tr_data_version_changes WHERE version_id IN ($version_chain_str_omit_base_version)) AND
		  			 d.data_id NOT IN (SELECT data_id FROM tr_data_version_deletions WHERE version_id IN ($version_chain_str_omit_base_version))
        ");
	}

  $result = mysql_fetch_assoc($count_query);

	return $result["c"];
}


/**
 * The sister function to ot_get_unreviewed_auto_translations. This returns the percentage
 * rather than the integer value. This is generally used in visualizing the  
 */
function ot_get_percentage_unreviewed_auto_translations($version_id, $language_id)
{
  // find the total number of data in this version
	$num_data = ot_get_num_version_data($version_id);
	
	// now the total number un-reviewed auto-translations
  $num_unreviewed_auto_translations = ot_get_unreviewed_auto_translations($version_id, $language_id);

	// do the math
	if ($num_data > 0)
  	$percentage = round($num_unreviewed_auto_translations / $num_data * 100);
	else
	  $percentage = 0;

	return $percentage;	
}