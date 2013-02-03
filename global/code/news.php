<?php

/*------------------------------------------------------------------------------------------------*\
  Copyright (C) 2010 Benjamin Keen
  http://www.opentranslate.org
\*------------------------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------------------------*\
  PROJECTS NEW - functions in brief

    ot_add_news
    ot_update_news_item
    ot_get_news
    ot_get_news_item
    ot_delete_news_item
    ot_get_unread_news_ids
    ot_get_news_item_read_by_translator_list
    ot_mark_news_as_read

\*------------------------------------------------------------------------------------------------*/


/**
 * Adds some new news for passing on to a project's translators.
 */
function ot_add_news_item($project_id, $info)
{
  $info = ot_clean_hash($info);

  $account_id = $_SESSION["ot"]["account_id"];
  $now = ot_get_current_datetime();
  $subject = $info["subject"];
  $message = $info["message"];
  $email_translators  = isset($info["email_translators"]) ? true : false;
  $send_summary_email = isset($info["send_summary_email"]) ? true : false;

  // add the news item
  $query = "
    INSERT INTO tr_project_news (project_id, status, creation_date, created_by, subject, message)
    VALUES ($project_id, 'online', '$now', $account_id, '$subject', '$message')
      ";

  mysql_query($query);

  // if required, email the translators
  if ($email_translators)
  {
    $translators = ot_get_project_translators($project_id, 1, true);

    $emails = array();
    foreach ($translators["results"] as $translator)
      $emails[] = $translator["email"];


    // get the current logged in user account info
    $account_info = ot_get_account($_SESSION["ot"]["account_id"]);
    $email = $account_info["email"];

    $headers = "";
    $bcc = join(",", $emails);

    $headers = "MIME-Version: 1.0\r\n"
             . "From: $email\r\n"
             . "Reply-to: $email\r\n"
             . "Bcc: $bcc\r\n"
             . "Content-type: text/html;charset=UTF-8";

    mail($email, "Open Translate News: $subject", $message, $headers);

    // if required, send a summary email to this person
    if ($send_summary_email)
    {
      $date = date("M jS, g:i A");
      $subject = "News item email summary";
      $email_content = "<p>News item emailed to the following translators on <b>$date</b>: </p>\n<ul>";

      foreach ($translators as $translator)
        $email_content .= "<li>{$translator['first_name']} {$translator['last_name']}, {$translator['email']}</li>\n";

      $email_content .= "</ul>\n<br />Subject: $subject<br/>Message: $message";

      $headers = "MIME-Version: 1.0\r\n"
               . "From: $email\r\n"
               . "Reply-to: $email\r\n"
               . "Content-type: text/html;charset=UTF-8";

      mail($email, $subject, $email_content, $headers);
    }
  }
}


/**
 * Updates a news item for passing on to a project's translators.
 *
 * TODO: option of EMAILING news
 */
function ot_update_news_item($news_id, $info)
{
  $info = ot_clean_hash($info);

  $account_id = $_SESSION["ot"]["account_id"];
  $now = ot_get_current_datetime();
  $subject = $info["subject"];
  $message = $info["message"];

  $query = "
    UPDATE tr_project_news
    SET    subject = '$subject',
           message = '$message'
    WHERE  news_id = $news_id
      ";

  mysql_query($query);
}


/**
 * Returns all news for a project, ordered reverse chronologically
 */
function ot_get_news($project_id)
{
  $query = mysql_query("
    SELECT *
    FROM   tr_project_news
    WHERE  project_id = $project_id
    ORDER BY news_id DESC
      ");

  $infohash = array();
  while ($row = mysql_fetch_assoc($query))
    $infohash[] = $row;

  return $infohash;
}


/**
 * Returns all news for a project, ordered reverse chronologically
 */
function ot_get_news_item($news_id)
{
  $query = mysql_query("
    SELECT *
    FROM   tr_project_news
    WHERE  news_id = $news_id
      ");

  $infohash = mysql_fetch_assoc($query);
  return $infohash;
}


function ot_delete_news_item($news_id)
{
  mysql_query("DELETE FROM tr_project_news_translators WHERE news_id = $news_id");
  mysql_query("DELETE FROM tr_project_news WHERE news_id = $news_id");
}


/**
 * Returns the news IDs of all unread news for a translator-project.
 */
function ot_get_unread_news_ids($translator_id, $project_id)
{
  $query = mysql_query("
    SELECT news_id
    FROM   tr_project_news
    WHERE  project_id = $project_id
    AND    news_id NOT IN
      (SELECT news_id FROM tr_project_news_translators WHERE translator_id = $translator_id)
        ");

  $news_ids = array();
  while ($row = mysql_fetch_assoc($query))
    $news_ids[] = $row["news_id"];

  return $news_ids;
}


/**
 * Gets a list of translator IDs that have read a particular news item - cumbersome name, but I
 * couldn't think of a better one...
 */
function ot_get_news_item_read_by_translator_list($news_id)
{
  $query = mysql_query("SELECT translator_id FROM tr_project_news_translators WHERE news_id = $news_id");

  $translator_ids = array();
  while ($row = mysql_fetch_assoc($query))
    $translator_ids[] = $row["translator_id"];

  return $translator_ids;
}


/**
 * Called whenever a translator reads a news item. Keeps track of who's read what!
 */
function ot_mark_news_as_read($translator_id, $news_id)
{
  mysql_query("DELETE FROM tr_project_news_translators WHERE news_id = $news_id AND translator_id = $translator_id");
  mysql_query("INSERT INTO tr_project_news_translators (news_id, translator_id) VALUES ($news_id, $translator_id)");
}

