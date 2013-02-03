<?php

/*------------------------------------------------------------------------------------------------*\
  Copyright (C) 2010 Benjamin Keen
  http://www.opentranslate.org
\*------------------------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------------------------*\
  QUESTIONS: functions in brief

    This file handles all functions relating to the project's Contact Us form, such as asking new
    questions, responding to questions, returning entire conversation threads, marking a question /
    response as read, etc. Note, settings relating to the contact us form - like email notifications -
    are located in the general.php file (N.B. move to settings.php?)

      ot_add_question
      ot_get_translator_project_questions
      ot_get_translator_project_question_thread
      ot_mark_responses_as_read
      ot_get_project_questions

\*------------------------------------------------------------------------------------------------*/



/**
 * Called by translators on the Contact Us page. This logs the question in the tr_project_questions
 * table and marks it as "unread" for the project manager.
 *
 * @param integer $translator_id
 * @param array $info
 */
function ot_add_question($translator_id, $info)
{
  $info = ot_clean_hash($info);

  $subject = $info["subject"];
  $message = $info["message"];
  $now     = ot_get_current_datetime();
  $project_id = $info["project_id"];

  $result = mysql_query("
    INSERT INTO tr_project_questions (project_id, account_id, creation_date, status, thread_status, subject, message)
    VALUES  ($project_id, $translator_id, '$now', 'unread', 'new', '$subject', '$message')
      ") or ot_handle_error(mysql_error());


  $success = true;
  $response = "";
  if ($result)
  {
  	$question_id = mysql_insert_id();
  	ot_send_general_question_notification_emails($translator_id, $question_id);

    $response = "The project manager(s) have been notified. Responses to your questions can be found on your <a href=\"messages/\" class=\"bold\">Message Board</a>.";
  }
  else
  {
    $success  = false;
    $response = "There was an error sending your email.";
  }

  return array($success, $response);
}


/**
 * Called by translators, project managers and/or administrators. This logs a response to a question and
 * emails the user to let them know there's been a response.
 */
function ot_add_response($account_id, $question_id, $info)
{
  $info = ot_clean_hash($info);

  $old_thread_status = $info["old_thread_status"];
  $thread_status = $info["thread_status"];
  $comment = $info["comment"];
  $project_id = $info["project_id"];
  $now = ot_get_current_datetime();

  $result = mysql_query("
    INSERT INTO tr_project_questions (project_id, account_id, creation_date, response_to_question_id, status, message)
    VALUES  ($project_id, $account_id, '$now', $question_id, 'unread', '$comment')
      ") or ot_handle_error(mysql_error());

  // if the status has changed, update the original question's thread_status value
  if ($thread_status != $old_thread_status)
  {
    mysql_query("UPDATE tr_project_questions SET thread_status = '$thread_status' WHERE question_id = $question_id");
  }

  $participant_ids = ot_get_question_thread_participants($question_id);

  // email all participants with a direct link to the message thread

  // ADMIN:
  // http://localhost:8888/open_translate/admin/projects/messages/question_thread.php?question_id=X

  // TRANSLATOR:
  // http://localhost:8888/open_translate/translators/data/translate.php?data_id=2151&target_language_id=82&version_id=14&tab=2


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
function ot_get_translator_project_questions($translator_id, $project_id, $page_num = 1)
{
  global $g_max_num_project_questions_per_page;

  // determine the LIMIT clause
  $first_item = ($page_num - 1) * $g_max_num_project_questions_per_page;
  $limit_clause = "LIMIT $first_item, $g_max_num_project_questions_per_page";

  // get the main threads
  $result = mysql_query("
    SELECT *
    FROM   tr_project_questions
    WHERE  account_id = $translator_id AND
           project_id    = $project_id AND
           response_to_question_id IS NULL
    ORDER BY creation_date DESC
    $limit_clause
      ");

  // the count query
  $count_result = mysql_query("
    SELECT count(*) as c
    FROM   tr_project_questions
    WHERE  account_id = $translator_id AND
           project_id    = $project_id AND
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
      FROM   tr_project_questions
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
 * Returns an entire thread.
 */
function ot_get_translator_project_question_thread($question_id)
{
  $result = mysql_query("
    SELECT *
    FROM   tr_project_questions
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
function ot_mark_responses_as_read($translator_id, $question_id)
{
  mysql_query("
    UPDATE tr_project_questions
    SET    status = 'read'
    WHERE  (question_id = $question_id OR response_to_question_id = $question_id) AND
           status = 'unread' AND
           account_id != $translator_id
      ") or die(mysql_error());
}


/**
 * Returns a list of all questions made for a project. Used by administrators / project managers on
 * their Messages board. Note: it only returns to the TOP level questions, not responses to the
 * original question. This function also returns the number of responses in a "num_responses" hash
 * key, which can be used for display purposes and a "unread_responses" key with the number of unread
 * responses.
 *
 * Note: "unread_responses" is specific to the account doing the querying; namely it only returns
 * records marked as "unread" that are NOT written by themselves.
 */
function ot_get_project_questions($project_id, $account_id, $page_num)
{
  global $g_max_num_project_questions_per_page;

  // determine the LIMIT clause
  $first_item = ($page_num - 1) * $g_max_num_project_questions_per_page;
  $limit_clause = "LIMIT $first_item, $g_max_num_project_questions_per_page";

  // get the main conversation threads
  $result = mysql_query("
    SELECT *
    FROM   tr_project_questions
    WHERE  project_id = $project_id AND
           response_to_question_id IS NULL
    ORDER BY creation_date DESC
    $limit_clause
      ");

  // the count query
  $count_result = mysql_query("
    SELECT count(*) as c
    FROM   tr_project_questions
    WHERE  project_id = $project_id AND
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
      FROM   tr_project_questions
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

  $return_hash["results"] = $infohash;
  $return_hash["num_results"] = $count_hash["c"];

  return $return_hash;
}


/**
 * This returns an array of account IDs of all participants in a question thread.
 * @param unknown_type $question_id
 * @return unknown_type
 */
function ot_get_question_thread_participants($question_id)
{
  $result = mysql_query("
    SELECT account_id
    FROM   tr_project_questions
    WHERE  question_id = $question_id OR
           response_to_question_id = $question_id
    GROUP BY account_id
      ");

  $infohash = array();
  while ($row = mysql_fetch_assoc($result))
    $infohash[] = $row["account_id"];

  return $infohash;
}


/**
 * Called whenever a general question is made - either an original question by a translator, or a response
 * to that question by a project manager / administrator. This function does the job of figuring out who
 * should be notified and constructs an email containing a direct link to the question thread.
 *
 * @param integer $sender_account_id - the ID of the user who made the question/response
 * @param integer $question_id
 */
function ot_send_general_question_notification_emails($sender_account_id, $question_id)
{

}


/**
 * Called whenever a general question is made - either an original question by a translator, or a response
 * to that question by a project manager / administrator. This function does the job of figuring out who
 * should be notified and constructs an email containing a direct link to the question thread.
 *
 * @param integer $sender_account_id - the ID of the user who made the question/response
 * @param integer $question_id
 */
function ot_send_data_question_notification_emails($sender_account_id, $question_id)
{

}

