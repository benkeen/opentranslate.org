<?php

/*------------------------------------------------------------------------------------------------*\
  Copyright (C) 2010 Benjamin Keen
  http://www.opentranslate.org
\*------------------------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------------------------*\
  DATA QUESTIONS: functions in brief

    This file handles all functions relating to the project's Contact Us form, such as asking new
    questions, responding to questions, returning entire conversation threads, marking a question /
    response as read, etc.

      ot_add_data_question
      ot_get_data_questions
      ot_send_notification_emails
      ot_add_data_response
      ot_get_translator_project_data_questions
      ot_get_translator_project_data_question_thread
      ot_mark_data_responses_as_read
      ot_get_project_data_questions

\*------------------------------------------------------------------------------------------------*/



/**
 * Called by TRANSLATORS on the Bulk Translate and Translate pages. This logs the question in the
 * tr_data_questions table and marks it as "unread" for the project manager. If there's already a
 * thread started about this particular data item & translator, it just adds it to the thread.
 *
 * *** will be expanded to allow administrators & project managers to ask questions of the
 *  translator, too.
 */
function ot_add_data_question($translator_id, $info)
{
  global $g_root_url;

  $info = ot_clean_hash($info);

  $subject = $info["subject"];
  if ($subject == "context")
    $subject = "In what context is the text used?";
  else
    $subject = $info["custom_subject"];

  $now         = ot_get_current_datetime();
  $message     = $info["message"];
  $project_id  = $info["project_id"];
  $version_id  = $info["version_id"];
  $data_id     = $info["data_id"];
  $language_id = $info["language_id"];

  if (empty($data_id))
    return array(false, "No data ID selected.");

  // find out if there's already a thread started for this data item and translator. If so, add the
  // comment as a response in the same thread
  $check_thread_query = mysql_query("
    SELECT *
    FROM   tr_data_questions
    WHERE  response_to_question_id IS NULL AND
           account_id = $translator_id AND
           data_id = $data_id
       ");
  if (mysql_num_rows($check_thread_query) > 0)
  {
    $question_info = mysql_fetch_assoc($check_thread_query);
    $question_id = $question_info["question_id"];
    list($result, $message) = ot_add_data_response($translator_id, $question_id, $info);
  }
  else
  {
    $result = mysql_query("
      INSERT INTO tr_data_questions (creation_date, status, project_id, data_id, language_id, account_id, subject, message)
      VALUES  ('$now', 'unread', $project_id, $data_id, $language_id, $translator_id, '$subject', '$message')
        ") or ot_handle_error(mysql_error());

    // get the project managers(s) and if they have "receive email notifications" enabled, send them
    // a notification email with a link to the question
    ot_send_notification_emails("project_manager", $project_id, $version_id, $data_id);
  }

  $success = true;
  $response = "";
  if ($result)
    $response = "The project manager(s) have been notified. Responses to your questions can be found here on the Question tab, and on your <a href=\"../messages/\" class=\"bold\">Message Board</a> for easy reference.";
  else
  {
    $success  = false;
    $response = "There was an error sending your email.";
  }

  return array($success, $response);
}


function ot_send_notification_emails($recipient, $project_id, $version_id, $data_id)
{
  if ($recipient == "project_manager")
  {
    $project_managers = ot_get_project_managers($project_id);

    foreach ($project_managers as $project_manager)
    {
      if ($project_manager["receive_email_notifications"] == "no")
        continue;

      // pull the project name out
      $project_name = "";
      foreach ($project_manager["projects"] as $project)
      {
        if ($project["project_id"] == $project_id)
          $project_name = $project["name"];
      }

      $pm_email = $project_manager["email"];
      $subject  = "Open Translate Question";
      $link     = "$g_root_url/login.php?redirect=view_question&redirect_values=" . urlencode("project_id=$project_id&data_id=$data_id&version_id=$version_id");
      $header   = "From: Open Translate <admin@opentranslate.org>\r\n";
      $message  = "You have been sent a translation question concerning an item in your project $project_name. Click "
                . "the link below to login and view the message.\n\n$link";

      mail($pm_email, $subject, $message, $header);
    }
  }
/*
  else if ($recipient == "translator")
  {
    $project_managers = get_project_managers($project_id);

    foreach ($project_managers as $project_manager)
    {
      if ($project_manager["receive_email_notifications"] == "no")
        continue;

      // pull the project name out
      $project_name = "";
      foreach ($project_manager["projects"] as $project)
      {
        if ($project["project_id"] == $project_id)
          $project_name = $project["name"];
      }

      $pm_email = $project_manager["email"];
      $subject  = "Open Translate Question";
      $link     = "$g_root_url/login.php?redirect=view_question&redirect_values=" . urlencode("project_id=$project_id&data_id=$data_id&version_id=$version_id");
      $header   = "From: Open Translate <admin@opentranslate.org>\r\n";
      $message  = "You have been sent a translation question concerning an item in your project $project_name. Click "
                . "the link below to login and view the message.\n\n$link";

      mail($pm_email, $subject, $message, $header);
    }
  }
*/
}


/**
 * Called by administrators & project managers. This function returns all question threads for a
 * particular data item.
 *
 * @params integer $data_id - the
 * @params integer $account_id - the account ID of the currently logged in admin / project manager
 */
function ot_get_data_questions($data_id, $account_id)
{
  // get the main threads
  $result = mysql_query("
    SELECT *
    FROM   tr_data_questions
    WHERE  data_id = $data_id AND
           response_to_question_id IS NULL
      ");

  // now stick it into an array of hashes, and add the num_responses and contains_unread_responses keys
  $infohash = array();

  while ($row = mysql_fetch_assoc($result))
  {
    $question_id = $row["question_id"];

    $info_query = mysql_query("
      SELECT status, account_id, count(status) as c
      FROM   tr_data_questions
      WHERE  response_to_question_id = $question_id
      GROUP BY status
        ") or die(mysql_error());

    $num_unread = 0;
    $num_responses = 0;

    while ($info_row = mysql_fetch_assoc($info_query))
    {
      if ($info_row["status"] == "unread" && $info_row["account_id"] != $account_id)
        $num_unread += $info_row["c"];

      $num_responses += $info_row["c"];
    }

    $row["num_responses"] = $num_responses;
    $row["unread_responses"] = $num_unread;
    $infohash[] = $row;
  }

  return $infohash;
}


/**
 * Called by translators, project managers and/or administrators. This logs a response to a
 * question about a piece of data.
 */
function ot_add_data_response($account_id, $question_id, $info)
{
  $info = ot_clean_hash($info);

  $message = mysql_real_escape_string($info["message"]);
  $project_id = $info["project_id"];
  $version_id = $info["version_id"];
  $data_id = $info["data_id"];
  $language_id = $info["language_id"];
  $now = ot_get_current_datetime();

  $result = mysql_query("
    INSERT INTO tr_data_questions (creation_date, response_to_question_id, status, project_id, data_id, language_id, account_id, message)
    VALUES  ('$now', $question_id, 'unread', $project_id, $data_id, $language_id, $account_id, '$message')
      ") or ot_handle_error(mysql_error());

  // get the project managers(s) and if they have "receive email notifications" enabled, send them
  // a notification email with a link to the question
  $account_type = $_SESSION["ot"]["account_type"];
  ot_send_notification_emails($account_type, $project_id, $version_id, $data_id);

  $success = true;
  $response = "";
  if ($result)
    $response = "Your comment has been added.";
  else
  {
    $success  = false;
    $response = "There was an error adding your comment.";
  }

  return array($success, $response);
}


/**
 * Returns a list of all questions that a translator has made for a project. Note: it only returns
 * to the TOP level questions, not responses to the original question. This function also returns
 * the number of responses in a "num_responses" hash key, which can be used for display purposes
 * and a "unread_responses" key with the number of unread responses.
 *
 * Note: "unread_responses" is specific to the account doing the querying; namely it only returns
 * records marked as "unread" that are NOT written by themselves.
 */
function ot_get_translator_project_data_questions($translator_id, $project_id, $page_num = 1)
{
  global $g_max_num_data_questions_per_page;

  // determine the LIMIT clause
  $first_item = ($page_num - 1) * $g_max_num_data_questions_per_page;
  $limit_clause = "LIMIT $first_item, $g_max_num_data_questions_per_page";

  // get the main threads
  $result = mysql_query("
    SELECT *
    FROM   tr_data_questions
    WHERE  account_id = $translator_id AND
           project_id = $project_id AND
           response_to_question_id IS NULL
    ORDER BY creation_date DESC
    $limit_clause
      ");

  // the count query
  $count_result = mysql_query("
    SELECT count(*) as c
    FROM   tr_data_questions
    WHERE  account_id = $translator_id AND
           project_id = $project_id AND
           response_to_question_id IS NULL
       ");
  $count_hash = mysql_fetch_assoc($count_result);


  // now still it into an array of hashes, and add the num_responses and contains_unread_responses keys
  $infohash = array();

  while ($row = mysql_fetch_assoc($result))
  {
    $question_id = $row["question_id"];

    $info_query = mysql_query("
      SELECT status, account_id, count(status) as c
      FROM   tr_data_questions
      WHERE  response_to_question_id = $question_id
      GROUP BY status
        ") or die(mysql_error());

    $num_unread = 0;
    $num_responses = 0;

    while ($info_row = mysql_fetch_assoc($info_query))
    {
      if ($info_row["status"] == "unread" && $info_row["account_id"] != $translator_id)
        $num_unread += $info_row["c"];

      $num_responses += $info_row["c"];
    }

    $row["num_responses"] = $num_responses;
    $row["unread_responses"] = $num_unread;
    $infohash[] = $row;
  }

  $return_hash["results"] = $infohash;
  $return_hash["num_results"] = $count_hash["c"];

  return $return_hash;
}


/**
 * Returns an entire conversation thread for a translator-data item.
 */
function ot_get_translator_project_data_question_thread($data_id, $translator_id)
{
  // find the thread's question ID
  $result = mysql_query("
    SELECT question_id
    FROM   tr_data_questions
    WHERE  account_id = $translator_id AND
           data_id = $data_id AND
           response_to_question_id IS NULL
      ") or die(mysql_error());
  $info = mysql_fetch_assoc($result);
  $question_id = isset($info["question_id"]) ? $info["question_id"] : "";

  // if there's no thread, just return
  if (empty($question_id))
    return;

  // now retrieve the entire thread history
  $result = mysql_query("
    SELECT *
    FROM   tr_data_questions
    WHERE  question_id = $question_id OR
           response_to_question_id = $question_id
    ORDER BY question_id
      ");

  $infohash = array();
  while ($row = mysql_fetch_assoc($result))
    $infohash[] = $row;

  return $infohash;
}


/**
 * Called whenever a user views a conversation thread; it marks all responses (and original question)
 * NOT written by that person as "read".
 *
 * Note: this implicitly assumes that conversations are only between two people. Once you throw a
 * second project manager into the mix, this won't work!
  */
function ot_mark_data_responses_as_read($account_id, $question_id)
{
  mysql_query("
    UPDATE tr_data_questions
    SET    status = 'read'
    WHERE  (question_id = $question_id OR response_to_question_id = $question_id) AND
           status = 'unread' AND
           account_id != $account_id
      ") or die(mysql_error());
}


/**
 * Returns a list of all questions made for a project. Note: it only returns to the TOP level
 * questions, not responses to the original question. This function also returns the number of
 * responses in a "num_responses" hash key, which can be used for display purposes and a
 * "unread_responses" key with the number of unread responses.
 *
 * Note: "unread_responses" is specific to the account doing the querying; namely it only returns
 * records marked as "unread" that are NOT written by themselves.
 */
function ot_get_project_data_questions($project_id, $account_id)
{
  // get the main conversation threads
  $result = mysql_query("
    SELECT *
    FROM   tr_data_questions
    WHERE  project_id = $project_id AND
           response_to_question_id IS NULL
      ");

  // now still it into an array of hashes, and add the num_responses and contains_unread_responses keys
  $infohash = array();

  while ($row = mysql_fetch_assoc($result))
  {
    $question_id = $row["question_id"];

    $info_query = mysql_query("
      SELECT status, account_id, count(status) as c
      FROM   tr_data_questions
      WHERE  response_to_question_id = $question_id
      GROUP BY status
        ") or die(mysql_error());

    $num_unread = 0;
    $num_responses = 0;

    while ($info_row = mysql_fetch_assoc($info_query))
    {
      if ($info_row["status"] == "unread" && $info_row["account_id"] != $account_id)
        $num_unread += $info_row["c"];

      $num_responses += $info_row["c"];
    }

    $row["num_responses"] = $num_responses;
    $row["unread_responses"] = $num_unread;
    $infohash[] = $row;
  }

  return $infohash;
}
