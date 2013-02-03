<?php

/*------------------------------------------------------------------------------------------------*\
  Copyright (C) 2010  Benjamin Keen
  http://www.opentranslate.org
\*------------------------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------------------------*\
  TRANSLATORS - functions in brief

    ot_add_translator
    ot_join_project
    ot_get_translator
    ot_get_translators
    ot_get_project_translators
    ot_get_translator_projects
    ot_get_translator_project_languages
    ot_get_all_available_projects
    ot_get_all_available_projects_by_language
    ot_update_translator
    ot_update_translator_projects
    ot_update_translator_project_settings
    ot_get_translator_project_settings
    ot_get_available_project_version_languages
    ot_get_reviewed_project_data_translations
    ot_delete_translator
    ot_get_all_translations_by_translator
    ot_get_translators_for_credit

\*------------------------------------------------------------------------------------------------*/


/**
 * Adds a translator. Called by administrators or project managers. If a project ID is being passed
 * with $info, it adds the translator to that project
 *
 * @param array $info - everything else ($_POST)
 */
function ot_add_translator($info)
{
  global $LANG, $g_base_dir;

  $info = ot_clean_hash($info);

  // find out if the email is already taken. If it is, return false
  $email = $info["email"];
  if (ot_email_is_taken($email))
    return array(false, $LANG["validation_email_taken"]);

  $now = ot_get_current_datetime();
  $current_account_id = $_SESSION["ot"]["account_id"];
  $languages_spoken = $info["selected_languages"];

  $status      = $info["status"];
  $ui_language_id = $info["ui_language_id"];
  $first_name  = $info["first_name"];
  $last_name   = $info["last_name"];
  $ui_num_data_per_page = $info["ui_num_data_per_page"];
  $password    = $info["password"];

  // all checks out. Add the new record
  $account_insert_query = mysql_query("
    INSERT INTO tr_accounts (account_id, account_type, status, date_created, created_by_account_id,
      last_modified, first_name, last_name, email, password, ui_language_id, ui_num_data_per_page)
    VALUES ($account_id, 'translator', '$status', '$now', $current_account_id, '$now', '$first_name', '$last_name',
      '$email', '$password', $ui_language_id, $ui_num_data_per_page)
	    ");

  // add the corresponding record in the translator table
  $default_bulk_translate_view = $info["default_bulk_translate_view"];
  $query = mysql_query("
    INSERT INTO tr_translators (translator_id, default_bulk_translate_view)
    VALUES ($account_id, '$default_bulk_translate_view')
	    ") or die(mysql_error());

  // add the translation languages
  foreach ($languages_spoken as $lang)
  {
    $language_id = $lang;
    $query = mysql_query("
      INSERT INTO tr_translator_languages (translator_id, language_id)
      VALUES ($account_id, $language_id)
  	    ");
  }

  // if there's a project ID specified (i.e. it's a project manager adding the translator to a project),
  // assign this new translator to the project
  if (isset($info["project_id"]))
    ot_add_translator_to_project($account_id, $info["project_id"], $languages_spoken);

  return array(true, "");
}


/**
 * Called by a translator to add him/herself to a project.
 *
 * Note: the project version must be VISIBLE.
 *
 * @param integer $translator_id
 * @param integer $project_id
 * @param array $language_ids is an array of language ID
 */
function ot_add_translator_to_project($translator_id, $project_id, $language_ids)
{
  global $LANG;

  // remove the ORIGINAL language from the $language_ids, if it happens to be there
  $project_info = ot_get_project($project_id);
  $origin_language_id = $project_info["origin_language_id"];
  if (in_array($origin_language_id, $language_ids))
    array_splice($language_ids, array_search($origin_language_id, $language_ids), 1);

  // delete any old references for this project-translator
  mysql_query("DELETE FROM tr_project_translators WHERE project_id = $project_id AND translator_id = $translator_id");

  if (!empty($language_ids))
  {
    // add this translator to this version-language (this assumes $language_ids contains a valid
    // subset of all the languages offered by this project version)
    foreach ($language_ids as $language_id)
    {
      mysql_query("
  	    INSERT INTO tr_project_translators (project_id, language_id, translator_id)
  		  VALUES ($project_id, $language_id, $translator_id)
  				") or die(mysql_error());
    }
  }

  return array(true, $LANG["text_settings_updated"]);
}


/**
 * This completely removes a translator from a project.
 *
 * @param integer $project_id
 * @param integer $translator_id
 */
function ot_remove_translator_from_project($project_id, $translator_id)
{
  mysql_query("
    DELETE FROM tr_project_translators
    WHERE translator_id = $translator_id AND
          project_id = $project_id
      ");
}


/**
 * Gets a hash of information about a translator. The languages they speak are in the "language_ids" key.
 *
 * @param integer $translator_id
 */
function ot_get_translator($translator_id)
{
  $query = mysql_query("
    SELECT *
		FROM   tr_accounts a, tr_translators t
		WHERE  t.translator_id = $translator_id
    AND    a.account_id = t.translator_id
	    ") or die(mysql_error());

	$infohash = array();
	while ($field = mysql_fetch_assoc($query))
    $infohash = $field;

  $query = mysql_query("
    SELECT *
		FROM   tr_translator_languages
		WHERE  translator_id = $translator_id
	    ");

  $language_ids = array();
  while ($info = mysql_fetch_assoc($query))
    $language_ids[] = $info["language_id"];
  $infohash["language_ids"] = $language_ids;

	return $infohash;
}


/**
 * Returns ALL translators, regardless of project.
 *
 * @return array an array of translator hashes.
 */
function ot_get_translators()
{
  $query = mysql_query("
    SELECT *
		FROM   tr_accounts a, tr_translators t
    WHERE  a.account_id = t.translator_id AND
           a.status != 'deleted'
	    ");

	$infohash = array();
	while ($field = mysql_fetch_assoc($query))
    $infohash[] = $field;

	return $infohash;
}


/**
 * Returns the IDs of all translators associated with a particular project.
 *
 * @param integer $project_id
 * @param integer $page_num
 * @param boolean $return_all (defaults to false)
 * @return array an array of translator hashes
 */
function ot_get_project_translators($project_id, $page_num = 1, $return_all = false)
{
  global $g_max_num_translators_per_page;

  // determine the LIMIT clause
  $limit_clause = "";
  if (!$return_all)
  {
    $first_item = ($page_num - 1) * $g_max_num_translators_per_page;
    $limit_clause = "LIMIT $first_item, $g_max_num_translators_per_page";
  }

  $query = mysql_query("
    SELECT pt.translator_id, a.first_name, a.last_name, a.email
    FROM   tr_project_translators pt, tr_accounts a
    WHERE  pt.project_id = $project_id AND
           a.account_id = pt.translator_id AND
           a.status != 'deleted'
    ORDER BY a.last_name
    $limit_clause
	    ") or die(mysql_error());


  $count_query = mysql_query("
    SELECT count(*) as c
    FROM   tr_project_translators pt, tr_accounts a
    WHERE  pt.project_id = $project_id AND
           a.account_id = pt.translator_id AND
           a.status != 'deleted'
    ORDER BY a.last_name
	    ") or die(mysql_error());
  $count_hash = mysql_fetch_assoc($count_query);

	$infohash = array();
	while ($field = mysql_fetch_assoc($query))
    $infohash[] = $field;

  $return_hash["results"] = $infohash;
  $return_hash["num_results"] = $count_hash["c"];

  return $return_hash;
}


/**
 * Retrieves all projects that are relevant for a particular translator - this includes projects
 * that they have joined AND those that they haven't. This includes project versions that are
 * set to "may_translate" = no, but "is_visible" = yes. This is done so that translators can
 * sign up for a project - and subsequently view the project data, but not be allowed to actually
 * translate the content.
 *
 * Note: this function does NOT return the actual languages that a translator is translating
 * this project into; e.g. Project A may be translatable into 10 languages, 3 of which the
 * translator speaks, but only one of whom he/she has signed up to help translate into. For that
 * level of detail, we use the get_translator_project_languages function.
 *
 * @param integer $translator_id
 * @return array
 */
function ot_get_translator_projects($translator_id)
{
  $project_query = mysql_query("
    SELECT *, p.last_modified as project_last_modified
		FROM   tr_projects p, tr_project_versions pv, tr_project_translators pt
		WHERE  pt.translator_id = $translator_id AND
           pt.project_id = pv.project_id AND
           pv.project_id = p.project_id AND
           pv.is_visible = 'yes' AND
           p.status = 'online'
    GROUP BY p.project_id
	    ");

	$infohash = array();
	while ($field = mysql_fetch_assoc($project_query))
    $infohash[] = $field;

	return $infohash;
}


/**
 * Returns a hash of project_ids => array(language_ids); i.e. all projects and languages within those
 * projects they're assigned to.
 */
function ot_get_translator_project_languages($translator_id)
{
  $query = mysql_query("
      SELECT p.project_id, pt.language_id
      FROM   tr_projects p, tr_project_translators pt
      WHERE  p.project_id = pt.project_id
      AND    pt.translator_id = $translator_id
    ") or die(mysql_error());

  $project_languages = array();
  while ($result = mysql_fetch_assoc($query))
  {
    $project_id  = $result["project_id"];
    $language_id = $result["language_id"];

    $project_languages[$project_id][] = $language_id;
  }

  return $project_languages;
}


/**
 * This retrieves ALL projects that are requiring translation between two languages. So, all that
 * meet this criteria:
 *   - project is public
 *   - project is translating between two languages that this translator speaks
 *   - at least one of the project versions is_visible
 *
 * Note: this returns a superset of ot_get_translator_projects - which returns only those projects
 * that a translator has already agreed to translate.
 *
 * @param integer $origin_language_id
 * @param integer $translation_language_id
 * @param return hash
 */
function ot_get_all_available_projects($origin_language_id, $translation_language_id)
{
  $query = "
    SELECT *
    FROM   tr_projects p, tr_project_versions pv, tr_project_languages pl
    WHERE  p.project_visibility = 'public' AND
           p.status = 'online' AND
           p.project_id = pv.project_id AND
           p.project_id = pl.project_id AND
           p.origin_language_id = $origin_language_id AND
           pl.language_id = $translation_language_id AND
           pv.is_visible = 'yes'
    GROUP BY p.project_id
           ";

  $result = mysql_query($query) or die(mysql_error());

  $infohash = array();
  while ($row = mysql_fetch_assoc($result))
    $infohash[] = $row;

  return $infohash;
}


/**
 * A complement to get_all_available_projects. This function returns all projects that:
 *   - is public
 *   - is online
 *   - requires translations between two of the languages passed in the language param
 *   - at least one of the project versions is_visible
 *
 * @param array $languages - an array of two or more languages
 * @return
 */
function ot_get_all_available_projects_by_language($translator_languages)
{
  $language_pairs = ot_get_distinct_ordered_size_2_subsets($translator_languages, false);
	
  $clauses = array();
  foreach ($language_pairs as $pair)
  {
    $first  = $pair[0];
    $second = $pair[1];
    $clauses[] = "(p.origin_language_id = $first AND pl.language_id = $second)";
  }
  $clauses_str = join(" OR\n ", $clauses);

  if (!empty($clauses_str))
    $clauses_str = "AND ($clauses_str)";

  // TODO the logic sound in this function? Looks overly verbose ...

  $query = "
    SELECT *, p.project_id as unique_project_id
    FROM   tr_projects p, tr_project_versions pv, tr_project_languages pl
    WHERE  p.project_visibility = 'public' AND
           p.status = 'online' AND
           p.project_id = pv.project_id AND
           pv.project_id = pl.project_id AND
           pv.is_visible = 'yes'
           $clauses_str
    GROUP BY p.project_id
               ";

  $result = mysql_query($query) or die("Problem. " . mysql_error());

  $infohash = array();
  while ($row = mysql_fetch_assoc($result))
  {
    // add only those languages that this translator speaks
    $project_id = $row["project_id"];
    $result2 = mysql_query("
      SELECT  language_id
      FROM    tr_project_languages pl
      WHERE   project_id = $project_id
        ");

    $languages = array();
    while ($langs = mysql_fetch_assoc($result2))
    {
      if (!in_array($langs["language_id"], $languages) && in_array($langs["language_id"], $translator_languages))
        $languages[] = $langs["language_id"];
    }

    $row["languages"] = $languages;
    $infohash[] = $row;
  }

  return $infohash;
}


/**
 * Updates a translator account. Called by administrators or project managers.
 *
 * @param string $updated_by "admin", "project_manager"
 * @param integer $translator_id
 */
function ot_update_translator($updated_by, $translator_id, $info)
{
  $now  = ot_get_current_datetime();
  $info = ot_clean_hash($info);

  if ($updated_by == "admin")
  {
    $email       = $info["email"];
    $password    = $info["password"];
    $status      = $info["status"];
    $ui_language_id = $info["ui_language_id"];
    $first_name  = $info["first_name"];
    $last_name   = $info["last_name"];
    $languages_spoken = $info["selected_languages"];

    $ui_num_data_per_page = $info["ui_num_data_per_page"];
    $default_bulk_translate_view = $info["default_bulk_translate_view"];
    $translation_disclaimer = $info["translation_disclaimer"];

    $account_update_query = "
      UPDATE tr_accounts
      SET    status = '$status',
             last_modified = '$now',
             first_name = '$first_name',
             last_name = '$last_name',
             email = '$email',
             password = '$password',
             ui_language_id = $ui_language_id,
             ui_num_data_per_page = $ui_num_data_per_page
      WHERE  account_id = $translator_id
  	    ";

    // update the account
    $translator_update_query = "
      UPDATE tr_translators
      SET    translation_disclaimer = '$translation_disclaimer',
             default_bulk_translate_view = '$default_bulk_translate_view'
      WHERE  translator_id = $translator_id
        ";
  }

  if ($updated_by == "project_manager")
  {
    $email       = $info["email"];
    $password    = $info["password"];
    $ui_language_id = $info["ui_language_id"];
    $first_name  = $info["first_name"];
    $last_name   = $info["last_name"];
    $languages_spoken = $info["selected_languages"];

    $account_update_query = "
      UPDATE tr_accounts
      SET    last_modified = '$now',
             first_name = '$first_name',
             last_name = '$last_name',
             email = '$email',
             password = '$password',
             ui_language_id = '$ui_language_id'
      WHERE  account_id = $translator_id
  	    ";
  }


  mysql_query($account_update_query) or die(mysql_error());

  if (isset($translator_update_query))
    mysql_query($translator_update_query) or die(mysql_error());


  // update the translation languages
  $query = mysql_query("DELETE FROM tr_translator_languages WHERE translator_id = $translator_id");

  foreach ($languages_spoken as $language_id)
  {
    mysql_query("INSERT INTO tr_translator_languages (translator_id, language_id) VALUES ($translator_id, $language_id)");
  }

  return array(true, "The translator account has been updated.");
}


/**
 * Updates all projects for a particular translator. This is a pretty powerful function, called by
 * the administrator only. When a translator is assigned to a particular project version language,
 * entries are added for each and every project version that allows that language as a translation
 * option.
 *
 * Assumption: expects a POST containing any number of fields of this format: selected_projects_X_Y
 * (array) - where X is the origin language, Y is the translation language, and the value is the
 * project ID.
 */
function ot_update_translator_projects($translator_id, $info)
{
  // delete all old associations. We can do this BULK, since the upcoming queries will update the
	// records for this translator for ALL projects versions
  mysql_query("
    DELETE FROM tr_project_translators
    WHERE  translator_id = $translator_id
      ") or die(mysql_error());

  // loop through all lang->lang groups (e.g. English->Spanish, German->French)
  foreach ($_POST as $key=>$value)
  {
    if (!preg_match("/^selected_projects/", $key))
      continue;

    $sections = split("_", $key);
    $origin_language_id      = $sections[2]; // why is this even passed along? A project has a UNIQUE source language...
    $translation_language_id = $sections[3];
    $project_ids = $value;

    // loop through all the selected project IDs in this lang->lang group
    for ($i=0; $i<count($project_ids); $i++)
    {
      $project_id = $project_ids[$i];

			// insert the unique record into tr_project_translators
      mysql_query("
			  INSERT INTO tr_project_translators (project_id, language_id, translator_id)
				VALUES ($project_id, $translation_language_id, $translator_id)
				  ");
    }
  }
}


/*------------------------------------------------------------------------------------------------*\
  Function:   update_translator_project_settings
  Purpose:    called on the translator's project settings page.
  Assumption: the relevant record in tr_translator_project_settings already exists (this can be
              ensured by calling get_translator_project_settings beforehand).
\*------------------------------------------------------------------------------------------------*/
function ot_update_translator_project_settings($translator_id, $project_id, $info)
{
  // first, update the list of languages this translator will be translating for this project
  // N.B. the return values aren't being used right now...
  $selected_translations = isset($info["selected_translations"]) ? $info["selected_translations"] : "";
  list($success, $message) = ot_add_translator_to_project($translator_id, $project_id, $selected_translations);

  $may_credit_translator = isset($info["may_credit_translator"]) ? "yes" : "no";

  mysql_query("
    UPDATE tr_translator_project_settings
    SET    may_credit_translator = '$may_credit_translator'
    WHERE  translator_id = $translator_id AND
           project_id = $project_id
      ");

  // ...
  return array(true, "Your project settings have been updated.");
}


/**
 * Returns all project settings for a particular translator's project. If the settings don't exist,
 * a new blank record is created and that is returned.
 */
function ot_get_translator_project_settings($translator_id, $project_id)
{
  $query = mysql_query("
    SELECT *
    FROM   tr_translator_project_settings
    WHERE  translator_id = $translator_id AND
           project_id = $project_id
      ");

  // if the record doesn't exist, add it then re-query the DB for the info
  if (mysql_num_rows($query) == 0)
  {
    mysql_query("
      INSERT INTO tr_translator_project_settings (translator_id, project_id)
      VALUES ($translator_id, $project_id)
        ") or die(mysql_error());

    $query = mysql_query("
      SELECT *
      FROM   tr_translator_project_settings
      WHERE  translator_id = $translator_id AND
             project_id = $project_id
        ");
  }

  $result = mysql_fetch_assoc($query);
  return $result;
}


/**
 * Returns an array of data_ids that a translator has reviewed for a specific project version
 * language. This information is used on the main translator page to know which translations
 * should be reviewable.
 */
function ot_get_reviewed_project_data_translations($version_id, $translator_id, $language_id)
{
  $query = mysql_query("
    SELECT dt.translation_id
    FROM   tr_data d, tr_data_translations dt, tr_data_translation_reviews dtr
    WHERE  d.data_id = dt.data_id AND
           dt.translation_id = dtr.translation_id AND
           d.version_id = $version_id AND
           dtr.translator_id = $translator_id AND
           dt.language_id = $language_id
      ");

  $translation_ids = array();
  while ($row = mysql_fetch_assoc($query))
    $translation_ids[] = $row["translation_id"];

  return $translation_ids;
}


/**
 * Deletes a translator. In fact, it only marks their account as "deleted" - the record will always
 * exist in the database, just not get returned in any query.
 */
function ot_delete_translator($translator_id)
{
  $query = mysql_query("
    UPDATE tr_accounts
    SET    status = 'deleted'
    WHERE  account_id = $translator_id
      ");
}


/**
 * Returns all translations by translator.
 */
function ot_get_all_translations_by_translator($translator_id)
{

}


/**
 * This function returns a list of translators who've helped translate this version-language,
 * and have checked the "Yes, you may credit me as a translator of this project" checkbox on their
 * project settings page.
 *
 * This function is "dumb" right now; it doesn't actually check to see if a translator has in fact
 * done any translations / reviews; it just checks to see if they're assigned to the project and
 * have checked the "credit me" field mentioned above.
 */
function ot_get_translators_for_credit($version_id, $language_id)
{
  $project_id = ot_get_project_id_from_version_id($version_id);

  $result = mysql_query("
    SELECT *
    FROM   tr_translator_project_settings tps, tr_project_translators pt, tr_accounts a
    WHERE  tps.project_id = $project_id AND
           tps.may_credit_translator = 'yes' AND
           pt.language_id = $language_id AND
					 pt.translator_id = a.account_id AND
           tps.translator_id = a.account_id
    GROUP BY a.account_id
      ") or die(mysql_error());

  $translators_to_credit = array();
  while ($record = mysql_fetch_assoc($result))
    $translators_to_credit[] = $record;

  return $translators_to_credit;
}
