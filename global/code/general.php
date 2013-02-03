<?php

/*------------------------------------------------------------------------------------------------*\
  Copyright (C) 2010 Benjamin Keen
  http://www.opentranslate.org
\*------------------------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------------------------*\
  GENERAL - functions in brief

    ot_db_connect
		ot_db_disconnect
    ot_check_permision
		ot_display_message
		ot_get_date
		ot_get_current_datetime
    ot_clean_hash
    ot_generate_password
    ot_load_field
		ot_log_event
    ot_email_is_taken
    ot_display_page_nav
    ot_get_distinct_ordered_size_2_subsets
    ot_display_stars
    ot_html_encode
    ot_clean_for_double_quoted_str

\*------------------------------------------------------------------------------------------------*/


/**
 * Connects to a database. After connecting, you should always call disconnect_db() to close it
 * when you're done.
 */
function ot_db_connect($db_hostname, $db_username, $db_password, $db_name)
{
  $link = @mysql_connect($db_hostname, $db_username, $db_password)
    or die("Couldn't connect to database: " . mysql_error());
  @mysql_select_db($db_name)
    or die ("couldn't find database '$db_name'.");

  @mysql_query("SET NAMES 'utf8'", $link);

  return $link;
}


/**
 * Disconnects from a database
 */
function ot_db_disconnect($link)
{
  @mysql_close($link);
}


/**
 * Checks the currently logged in person
 */
function ot_check_permission($permission)
{
  global $g_login_url, $g_session_timeout;

	$log_user_out = false;

	// first, check all account info is in sessions
	if (!isset($_SESSION["ot"]) || !isset($_SESSION["ot"]["account_id"]) ||
	    !isset($_SESSION["ot"]["account_type"]) || !isset($_SESSION["ot"]["account_pwd"]))
    $log_user_out = true;

	else if ($permission == "admin" && $_SESSION["ot"]["account_type"] != "admin")
    $log_user_out = true;

	else if ($permission == "project_manager" && $_SESSION["ot"]["account_type"] == "translator")
    $log_user_out = true;

	if ($log_user_out)
	{
    // unlock any sessions this user may have
    if (!empty($_SESSION["ot"]["account_id"]) && is_numeric($_SESSION["ot"]["account_id"]))
      ot_unlock_all_account_sessions($_SESSION["ot"]["account_id"]);

	  header("location: $g_login_url");
		exit;
	}

  // second, check their session hasn't timed-out
  $now = date("U");
  if (($_SESSION["ot"]["last_activity_unixtime"] + $g_session_timeout) > $now)
    $_SESSION["ot"]["last_activity_unixtime"] += $g_session_timeout;
  else
  {
    // unlock any sessions this user may have
    if (!empty($_SESSION["ot"]["account_id"]) && is_numeric($_SESSION["ot"]["account_id"]))
      ot_unlock_all_account_sessions($_SESSION["ot"]["account_id"]);

	  header("location: $g_login_url?message=session_timeout");
		exit;
	}
}


/**
 * This helper function is used throughout the site to output messages to the user, the content
 * of which are returned by the various functions. It can handle multiple messages (notifications
 * and/or errors) by passing in arrays for each of the two parameters.
 *
 * @param mixed results - either a boolean (true / false) or an array of booleans. This indicates
 *              whether an action was successful or not.
 * @param mixed messages - message to output, or an array of messages. The index of which corresponds
 *              to the success/failure boolean in the $results parameter
 */
function ot_display_message($results, $messages)
{
  global $LANG;

  // if there are no messages, just return
  if (empty($messages))
    return;

  $notifications = array();
  $errors        = array();

  if (is_array($results))
  {
    for ($i=0; $i<count($results); $i++)
    {
      if     ($results[$i])  $notifications[] = $messages[$i];
      elseif (!$results[$i]) $errors[]        = $messages[$i];
    }
  }
  else
  {
    if     ($results)  $notifications[] = $messages;
    elseif (!$results) $errors[]        = $messages;
  }


  // display notifications
  if (!empty($notifications))
  {
    if (count($notifications) > 1)
    {
      array_walk($notifications, create_function('&$el','$el = "&bull;&nbsp; " . $el;'));
      $display_str = join("<br />", $notifications);
    }
    else
      $display_str = $notifications[0];

    echo "<div class='notify'><span><span><span><span><span><span><span><span>$display_str</span></span></span></span></span></span></span></span></div><br />";
  }

  // display errors
  if (!empty($errors))
  {
    // if there were notifications displayed, add a little padding to separate the two sections
    if (!empty($notifications)) { echo "<br />"; }

    if (count($errors) > 1)
    {
      array_walk($errors, create_function('&$el','$el = "&bull;&nbsp; " . $el;'));
      $display_str = join("<br />", $errors);
      $title_str = $LANG["word_errors"];;
    }
    else
    {
      $display_str = $errors[0];
      $title_str = $LANG["word_error"];
    }

    echo "<div class='error'><span>$title_str</span><br /><br />$display_str</div><br />";
  }
}


/**
 * Helper function to return a date according based on an offset and a display value.
 *
 * @param integer $offset   - the GMT offset
 * @param string $datetime - the mysql datetime to format
 * @param string $format   - the format to use (PHP's date() function).
 */
function ot_get_date($offset, $datetime, $format)
{
  $year = substr($datetime,0,4);
  $mon  = substr($datetime,5,2);
  $day  = substr($datetime,8,2);
  $hour = substr($datetime,11,2);
  $min  = substr($datetime,14,2);
  $sec  = substr($datetime,17,2);

  return date($format, mktime($hour + $offset, $min, $sec, $mon, $day, $year));
}


/**
 * Returns current datetime for MySQL
 */
function ot_get_current_datetime()
{
  return date("Y-m-d H:i:s");
}


/**
 * Helper function. Should be used on all POST data to escape user-inputted values. Not recursive;
 * only works for one layer of arrays.
 */
function ot_clean_hash($input)
{
  if (is_array($input))
  {
    $output = array();
    foreach ($input as $k=>$i)
      $output[$k] = ot_clean_hash($i);
  }
  else
  {
    if (get_magic_quotes_gpc())
      $input = stripslashes($input);

    $output = mysql_real_escape_string($input);
  }

  return $output;
}


/**
 * @param string $length - any integer value, specifying the length of the password.
 */
function ot_generate_password($length = 8)
{
  $password = "";
  $possible = "0123456789bcdfghjkmnpqrstvwxyz";
  $i=0;

  // add random characters to $password until $length is reached
  while ($i < $length)
  {
    // pick a random character from the possible ones
    $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);

    // we don't want this character if it's already in the password
    if (!strstr($password, $char)) {
      $password .= $char;
      $i++;
    }
  }
  return $password;
}


/**
 * This invaluable little function is used for storing and overwriting the contents of a single
 * form field in sessions based on a sequence of priorities.
 *
 * It assumes that a variable value can be found in GET, POST or SESSIONS (or all three). It then
 * returns the value stored in the most important variable (GET first, POST second, SESSIONS
 * third), and update sessions at the same time. This is extremely helpful in situations where you
 * need to store information in sessions, but possibly overwrite it depending on what the user
 * selected.
 *
 * @param string $field_name The field name.
 * @param string $session_name The session key for this field name.
 * @return string The field content.
 */
function ot_load_field($field_name, $session_name, $default_value = "")
{
  $field = $default_value;

  if (isset($_GET[$field_name]))
  {
    $field = $_GET[$field_name];
    $_SESSION["ot"][$session_name] = $field;
  }
  else if (isset($_POST[$field_name]))
  {
    $field = $_POST[$field_name];
    $_SESSION["ot"][$session_name] = $field;
  }
  else if (isset($_SESSION["ot"][$session_name]))
    $field = $_SESSION["ot"][$session_name];

  return $field;
}


/**
 * Generic function to log events in one way or other. e.g. logging in, stores the date the user
 * logged in at.
 */
function ot_log_event($event, $info)
{
  switch ($event)
	{
	  case "login":
		 $account_id = $info;
		 $now = date("Y-m-d H:i:s");

		 $query = mysql_query("
  		 UPDATE tr_accounts
  		 SET    last_logged_in = '$now'
  		 WHERE  account_id = $account_id
			   ");
		 break;
	}
}


/**
 * Checks to see if an email address is already in the database. Returns true it if is, returns
 * false if it isn't.
 */
function ot_email_is_taken($email)
{
  $query = mysql_query("
		 SELECT count(*)
     FROM tr_accounts
		 WHERE  email = '$email'
	     ");

  $result = mysql_fetch_array($query);

  $is_taken = (isset($result[0]) && $result[0] > 0) ? true : false;

  return $is_taken;
}


/**
 * Displays basic &lt;&lt; 1 2 3 >> navigation for lists, each linking to the current page.
 *
 * @param integer $num_results The total number of results found.
 * @param integer $num_per_page The max number of results to list per page.
 * @param integer $current_page The current page number being examined (defaults to 1).
 * @param string $pass_along_str The string to include in nav links.
 */
function ot_display_page_nav($num_results, $num_per_page, $current_page = 1, $pass_along_str = "", $page_link = "page")
{
  global $g_max_nav_pages, $LANG;

  // display the total number of results found
  $range_start = ($current_page - 1) * $num_per_page + 1;
  $range_end   = $range_start + $num_per_page - 1;
  $range_end   = ($range_end > $num_results) ? $num_results : $range_end;

  echo "<div style=\"margin-bottom: 7px;\">{$LANG['label_total_results']} <b>$num_results</b>&nbsp;";

  // if there's more than one page, display a message showing which numbers are being shown
  if ($num_results > $num_per_page)
  {
    // expects $LANG["label_viewing_result_range"] to be a string of form "[viewing %%x%% to %%y%%]"
    $display_string = preg_replace("/%%x%%/", $range_start, $LANG["label_viewing_result_range"]);
    $display_string = preg_replace("/%%y%%/", $range_end, $display_string);
    echo " $display_string";
  }

  // calculate total number of pages
  $total_pages  = ceil($num_results / $num_per_page);

  // piece together additional query string values
  $query_str = (!empty($pass_along_str)) ? "&{$pass_along_str}" : "";

  // determine the first and last pages to show page nav links for
  $half_total_nav_pages  = floor($g_max_nav_pages / 2);
  $first_page = ($current_page > $half_total_nav_pages) ? $current_page - $half_total_nav_pages : 1;
  $last_page  = (($current_page + $half_total_nav_pages) < $total_pages) ? $current_page + $half_total_nav_pages : $total_pages;

  if ($total_pages > 1)
  {
    echo "<br />{$LANG['word_page']}: ";

    for ($page=$first_page; $page<=$last_page; $page++)
    {
      // if we're not on the first page, provide a "<<" (previous page) link
      if ($current_page != 1 && $page == $first_page)
        echo "<a href='{$_SERVER['PHP_SELF']}?{$page_link}=" . ($current_page-1) . "{$query_str}'>&laquo;</a>&nbsp;";

      echo "<a href='{$_SERVER['PHP_SELF']}?{$page_link}=$page{$query_str}'>";
      if ($page == $current_page)
        echo "<b>$page</b>";
      else
        echo $page;

      echo "</a> ";

      // if required, add a final ">>" (next page) link
      if ($current_page != $total_pages && $page == $last_page)
        echo "<a href='{$_SERVER['SCRIPT_NAME']}?{$page_link}=" . ($current_page+1) . "{$query_str}'>&raquo;</a>";
    }
  }

  echo "</div>";
}


// returns all distinct, ordered, size 2 subsets of an array. The kludgy second parameter
// requires that a
function ot_get_distinct_ordered_size_2_subsets($array, $require_more_than_2_elements = true)
{
  $subsets = array();

  // if the array only contains two elements, just return it
  if ($require_more_than_2_elements && count($array) == 2)
    return array($array);

  for ($i=0; $i<count($array); $i++)
  {
    $curr_el1 = $array[$i];

    for ($j=0; $j<count($array); $j++)
    {
      $curr_el2 = $array[$j];

      if ($curr_el1 == $curr_el2)
        continue;

      // if an element of [$curr_el1, $curr_el2] and [$curr_el2, $curr_el1] doesn't exist,
      // add if to $subsets
      if (!in_array(array($curr_el1, $curr_el2), $subsets))
        $subsets[] = array($curr_el1, $curr_el2);
    }
  }

  return $subsets;
}


/**
 * Checks to see if an email address is already in the database. Returns true it if is, returns false
 * if it isn't.
 */
function ot_convert_datetime_to_timestamp($datetime)
{
  list($date, $time) = explode(" ", $datetime);
  list($year, $month, $day) = explode("-", $date);
  list($hours, $minutes, $seconds) = explode(":", $time);

  return mktime($hours, $minutes, $seconds, $month, $day, $year);
}


/**
 * Displays the number of stars specified in the parameter.
 */
function ot_display_stars($num)
{
	global $g_root_url;

  if ($num <= 0)
    return;

  echo "<table cellspacing='0' cellpadding='0'><tr>";

  for ($i=0; $i<$num; $i++)
    echo "<td class='no_underline'><img src='$g_root_url/images/star.jpg'></td>";

  echo "</tr></table>";
}


/**
 * Returns true if the variable is a hash.
 */
function ot_is_hash($var)
{
  if (!is_array($var))
    return false;

  return array_keys($var) !== range(0,sizeof($var)-1);
}


/**
 * Encodes HTML safely for UTF-8. Use instead of htmlentities.
 *
 * @param string $var
 * @return string
 */
function ot_html_encode($var)
{
	return htmlentities($var, ENT_QUOTES, 'UTF-8') ;
}


// ensures that a string ONLY escapes double-quotes
function ot_clean_for_double_quoted_str($string)
{
  $string = preg_replace('/\\\/', "", $string);
  $string = preg_replace('/\"/', "\\\"", $string);
  $string = preg_replace("/\\$/", '\\\$', $string); //the double quotes on the first field are intentional! 
	$string = preg_replace('/&amp;/', '&', $string);
	
  return $string;
}


function ot_multiarray_keys($array)
{
	$keys = array();
  foreach ($array as $k => $v)
  {
    $keys[] = $k;
    if (is_array($array[$k]))
    {
      $keys = array_merge($keys, ot_multiarray_keys($array[$k]));
    }
  }

  return $keys;
}


/**
 * Searches a tree for a particular key. The key is a multi-dimensional tree of any size whose
 * keys are UNIQUE. If they aren't, this function will just return the first subtree whose key
 * is found.
 *
 * @param hash $tree
 * @param string $target
 * @return hash
 */
function ot_get_tree_fragment($tree, $target)
{
  $keys = array_keys($tree);

  foreach ($keys as $key)
  {
  	if ($key == $target)
  	{
  	  return array(
  	    $target => $tree[$key]
  	      );
  	}
  	else
  	{
  		// if we've found a tree fragment in this iteration, return it! Otherwise let the
  		// search of this particular branch just end
  	  $result = ot_get_tree_fragment($tree[$key], $target);
  	  if (!empty($result))
  	    return $result;
  	}
  }
}

