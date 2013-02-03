<?php

/*------------------------------------------------------------------------------------------------*\
  Copyright (C) 2010 Benjamin Keen
  http://www.opentranslate.org
\*------------------------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------------------------*\
  REPORTS - functions in brief

    ot_get_project_activity

\*------------------------------------------------------------------------------------------------*/


function ot_get_project_activity($project_ids, $duration = 1)
{
  global $g_root_url;

  $query = mysql_query("
    SELECT dt.translator_id, d.version_id, dt.language_id, count( * ) as num_translations
    FROM tr_data_translations dt, tr_data d, tr_accounts a
    WHERE dt.data_id = d.data_id AND
          dt.translator_id = a.account_id AND
          a.account_type = 'translator' AND
          DATE_ADD(dt.creation_date, INTERVAL $duration DAY) > curdate()
    GROUP BY dt.translator_id, d.version_id, dt.language_id
      ") or ot_handle_error(mysql_error());

  $infohash = array();
  while ($row = mysql_fetch_assoc($query))
    $infohash[] = $row;

  return $infohash;
}