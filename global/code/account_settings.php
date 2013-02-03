<?php

/*------------------------------------------------------------------------------------------------*\
  Copyright (C) 2010 Benjamin Keen
  http://www.opentranslate.org
\*------------------------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------------------------*\
  ACCOUNTS SETTINGS - functions in brief

    ot_get_account_settings
    ot_update_account_settings_translations_page

\*------------------------------------------------------------------------------------------------*/


/**
 * Returns whatever settings are stored in the account_settings table for this particular user.
 * Returns a hash.
 */
function ot_get_account_settings($account_id)
{
  global $g_root_url;

  $query = mysql_query("
    SELECT *
    FROM   tr_account_settings
    WHERE  account_id = $account_id
      ") or ot_handle_error(mysql_error());

  $infohash = array();
  while ($row = mysql_fetch_assoc($query))
    $infohash[$row["setting_name"]] = $row["setting_value"];

  return $infohash;
}


/**
 * Used on the "translations" page for administrators.
 */
function ot_update_account_settings_translations_page($account_id, $version_id, $info)
{
  global $g_root_url;

  $num_per_page = $info['ui_num_data_per_page'];
  $columns = implode(",", $info['columns']);

  // if no records exists for this account_id and setting_name ("version_[id]_data_columns" and
  // "version_[id]_ui_num_data_per_page"), add them

  // this sucks, since the first time project managers go to view the page they'll see some dud info.
  $query1 = mysql_query("SELECT * FROM tr_account_settings WHERE account_id = $account_id and setting_name = 'version_{$version_id}_ui_num_data_per_page'");
  $result1 = mysql_fetch_assoc($query1);
  if (empty($result1))
  {
		mysql_query("
			INSERT INTO tr_account_settings (account_id, setting_name, setting_value)
			VALUES ($account_id, 'version_{$version_id}_ui_num_data_per_page', '$num_per_page')
			  ") or ot_handle_error(mysql_error());
  }
  else
  {
		mysql_query("
			UPDATE tr_account_settings
			SET    setting_value = $num_per_page
			WHERE  account_id = $account_id AND
						 setting_name = 'version_{$version_id}_ui_num_data_per_page'
							 ") or ot_handle_error(mysql_error());
  }


  $query2 = mysql_query("SELECT * FROM tr_account_settings WHERE account_id = $account_id and setting_name = 'version_{$version_id}_data_columns'");
  $result2 = mysql_fetch_assoc($query2);
  if (empty($result2))
  {
		mysql_query("
			INSERT INTO tr_account_settings (account_id, setting_name, setting_value)
			VALUES ($account_id, 'version_{$version_id}_data_columns', '$columns')
			  ") or ot_handle_error(mysql_error());
  }
  else
  {
		mysql_query("
			UPDATE tr_account_settings
			SET    setting_value = '$columns'
			WHERE  account_id = $account_id AND
						 setting_name = 'version_{$version_id}_data_columns'
							 ") or ot_handle_error(mysql_error());
	}
}
