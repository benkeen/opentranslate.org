<?php

/*------------------------------------------------------------------------------------------------*\
  Copyright (C) 2010 Benjamin Keen
  http://www.opentranslate.org
\*------------------------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------------------------*\
  LOCKING - functions in brief

    ot_lock_translation_language
    ot_unlock_translation_language
    ot_unlock_all_account_sessions
    ot_unlock_expired_sessions

\*------------------------------------------------------------------------------------------------*/


/**
 * Locks a particular translation of a data item for a particular user. This is used whenever a
 * translator selects a data item for translation. The lock is relinquished in two ways:
 *   1. the database has been updated with the translation
 *   2. the lock time expires. For version 1, this lock time is hardcoded to 10 minutes regardless
 *      of data size.
 *
 * This function also checks to see if an existing lock has been made for this data-translation.
 * If it has it returns false. Otherwise it returns true.
 *
 * @return boolean TRUE is lock is successfully made. FALSE if not.
 */
function ot_lock_translation_language($account_id, $data_id, $language_id)
{
  global $g_LOCK_TIME;

  // see if someone else has already locked this data-language
  $query = mysql_query("
    SELECT *
    FROM   tr_session_locked_data_language
    WHERE  data_id = $data_id AND
           language_id = $language_id AND
           account_id != $account_id
             ") or ot_handle_error(mysql_error());;
  if (mysql_num_rows($query) > 0)
     return false;


  // we're good. Delete any existing lock and re-establish the lock on this data item
  mysql_query("DELETE FROM tr_session_locked_data_language WHERE data_id = $data_id AND language_id = $language_id");

  $lock_start = date("U");
  $lock_end   = $lock_start + $g_LOCK_TIME;

  // now create lock
  $query = mysql_query("
    INSERT INTO tr_session_locked_data_language (account_id, data_id, language_id, lock_start, lock_end)
    VALUES ($account_id, $data_id, $language_id, '$lock_start', '$lock_end')
      ") or ot_handle_error(mysql_error());
}

/**
 * Unlocks a particular translation of a data item. Called after a translator has finished translating an item.
 */
function ot_unlock_translation_language($data_id, $language_id)
{
  mysql_query("DELETE FROM tr_session_locked_data_language WHERE data_id = $data_id AND language_id = $language_id");
}


/**
 * Unlocks any locks made by a particular account
 */
function ot_unlock_all_account_sessions($account_id)
{
  mysql_query("DELETE FROM tr_session_locked_data WHERE account_id = $account_id");
  mysql_query("DELETE FROM tr_session_locked_data_language WHERE account_id = $account_id");
  mysql_query("DELETE FROM tr_session_locked_translations WHERE account_id = $account_id");
}


/**
 * Unlocks any sessions that have expired. This will be placed in a 10 minute cron, but for now it's
 * called every time a translator goes to their main translation page.
 */
function ot_unlock_expired_sessions()
{
  $now = date("U");

  mysql_query("DELETE FROM tr_session_locked_data WHERE lock_end < $now");
  mysql_query("DELETE FROM tr_session_locked_data_language WHERE lock_end < $now");
  mysql_query("DELETE FROM tr_session_locked_translations WHERE lock_end < $now");
}
