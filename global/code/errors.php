<?php

/*------------------------------------------------------------------------------------------------*\
  Copyright (C) 2010 Benjamin Keen
  http://www.opentranslate.org
\*------------------------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------------------------*\
  ERRORS - functions in brief

    ot_handle_error

\*------------------------------------------------------------------------------------------------*/


/**
 * General error handler for severe error messages; e.g. queries that shouldn't have failed. This
 * function sends an email containing the error details, the user sessions and the time of the
 * error, in the hope that that will help debug the problem.
 */
function ot_handle_error($error_message)
{
  global $g_login_url;

  $recipient = "ben.keen@gmail.com";
  $headers   = "Content-Type: text/html;";

  $sessions = "<p><b>User Sessions</b></p><ul>";
  while (list($key, $value) = each($_SESSION["ot"]))
    $sessions .= "<li>$key: $value</li>\n";
  $sessions .= "</ul>";

  $time = "Time of error: " . date("M jS Y, g:i A");

  $message = "<p><b>Error</b></p>
              <p>$error_message</p>
              $sessions
              <p>$time</p>";

  mail($recipient, "Open Translate: Error", $message, $headers);

  // now boot the person out
  header("location: $g_login_url?message=permanent_error");
}
