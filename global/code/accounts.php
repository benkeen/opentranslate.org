<?php

/**
 * @copyright (C) 2010 Benjamin Keen
 * http://www.opentranslate.org
 */

/*------------------------------------------------------------------------------------------------*\
  ACCOUNTS - functions in brief

    ot_login
    ot_get_account
		ot_update_account

\*------------------------------------------------------------------------------------------------*/


/**
 * Logs a user in, regardless of account type (admin / project_leader, translator).
 *
 * @return string error message string (if error occurs). Otherwise it redirects the user to the
 * appropriate index page.
 */
function ot_login($info)
{
  global $g_root_url, $g_encryption_salt, $LANG;

  $form_vals = ot_clean_hash($info);
  $password = $form_vals["password"];
  $email    = trim($form_vals["email"]);

  $query  = mysql_query("
          SELECT account_id, account_type, status, email, password, ui_num_data_per_page
          FROM   tr_accounts
          WHERE  email = '$email'
          ") or die(mysql_error());
  $account_info = mysql_fetch_assoc($query);

  // error check user login info
  if (empty($email))                          return array(false, $LANG["validation_login_no_email"]);
  if (empty($password))                       return array(false, $LANG["validation_login_no_password"]);
  if (empty($account_info))                   return array(false, $LANG["validation_login_unknown_email"]);
  if ($account_info['status'] == 'disabled')  return array(false, $LANG["validation_login_disabled_account"]);
  if ($password != $account_info['password']) return array(false, $LANG["validation_login_incorrect_password"]);


  // all checks out. Log them in
  $login_redirect        = isset($_SESSION["ot"]["login_redirect"]) ? $_SESSION["ot"]["login_redirect"] : "";
  $login_redirect_values = isset($_SESSION["ot"]["login_redirect_values"]) ? urldecode($_SESSION["ot"]["login_redirect_values"]) : "";

  // store account-type specific values in sessions
  $_SESSION["ot"] = array();
  $_SESSION['ot']['account_id']   = $account_info['account_id'];
  $_SESSION['ot']['account_type'] = $account_info['account_type'];
  $_SESSION['ot']['account_pwd']  = crypt($account_info['password'], $g_encryption_salt);
  $_SESSION['ot']['ui_num_data_per_page'] = $account_info['ui_num_data_per_page'];

  // keep track of when this person logged in
  ot_log_event("login", $_SESSION['ot']['account_id']);

  // store the time in sessions. Used for timeout calculations
  $_SESSION['ot']['last_activity_unixtime'] = date("U");

  switch ($account_info['account_type'])
  {
    case "admin":
      header("Location: $g_root_url/admin/");
      break;

    case "project_manager":
      $url = "";

      switch ($login_redirect)
      {
        case "view_question":
          parse_str($login_redirect_values, $values);

          $project_id = $values["project_id"];
          $version_id = $values["version_id"];
          $data_id    = $values["data_id"];

          // store the appropriate settings in sessions and redirect
          $_SESSION["ot"]["project_id"] = $project_id;
          $_SESSION["ot"]["version_id"] = $version_id;

          $project  = ot_get_project($_SESSION["ot"]["project_id"]);
          $_SESSION["ot"]["project_name"] = $project["name"];

          $url = "/admin/projects/data/edit_data.php?data_id=$data_id&version_id=$version_id&tab=3";
          break;

        default:
          $url = "$g_root_url/admin/";
          break;
      }
      header("Location: $url");
      break;

    case "translator":
      header("Location: $g_root_url/translators/");
      break;
  }

  exit;
}


/**
 * A hash of information about the user account, regardless of account type.
 *
 * Assumption:  there are NO DB FIELDS NAMED THE SAME in the translators, project_managers and accounts
 * table. This function uses LEFT JOINS to return the whole shebang, so if the project_managers and
 * the translators account both had a field called "myfield", it would only return one value -
 * potentially overriding the correct one.
 */
function ot_get_account($account_id)
{
  global $g_root_url;

  $query = mysql_query("
    SELECT *
    FROM   tr_accounts a
      LEFT JOIN tr_project_managers pm ON a.account_id = pm.project_manager_id
      LEFT JOIN tr_translators t ON a.account_id = t.translator_id
    WHERE  a.account_id = $account_id
      ") or die(mysql_error());

  $info = mysql_fetch_assoc($query);

  return $info;
}


/**
 * Updates a translator or admin user account. *** This is called when a user is updating their OWN user
 * account. Otherwise, if it's an admin/project manager, the update_translator function is used. ***
 *
 * TODO This function contains a lot of redundancy.
 *
 * @param integer $account_id
 * @param array $info
 */
function ot_update_account($account_id, $info)
{
  global $LANG, $g_base_dir, $g_ot_db_name;

  $info = ot_clean_hash($info);

  $account_type = $info["account_type"];
  $now = ot_get_current_datetime();


  switch ($account_type)
  {
    case "admin":
      $email    = $info["email"];
      $password = $info["password"];
      $ui_language_id = $info["ui_language_id"];
      $first_name = $info["first_name"];
      $last_name  = $info["last_name"];
      $ui_num_data_per_page = $info["ui_num_data_per_page"];

      $query = mysql_query("
        UPDATE tr_accounts
        SET    last_modified = '$now',
               first_name = '$first_name',
               last_name = '$last_name',
               email = '$email',
               password = '$password',
               ui_language_id = $ui_language_id,
               ui_num_data_per_page = $ui_num_data_per_page
        WHERE  account_id = $account_id
          ") or ot_handle_error(mysql_error());

      // update Sessions
      $_SESSION["ot"]["ui_num_data_per_page"] = $ui_num_data_per_page;
      break;


		case "translator":
      $email    = $info["email"];
      $password = $info["password"];
      $ui_language_id = (isset($info["ui_language_id"]) && !empty($info["ui_language_id"])) ? $info["ui_language_id"] : 25;
      $first_name = $info["first_name"];
      $last_name  = $info["last_name"];
      $selected_languages = $info["selected_languages"];
      $translation_disclaimer = $info["translation_disclaimer"];
      $ui_num_data_per_page = $info["ui_num_data_per_page"];
      $default_bulk_translate_view = $info["default_bulk_translate_view"];
      $receive_email_notifications = isset($info["receive_email_notifications"]) ? $info["receive_email_notifications"] : "yes";

      $query = mysql_query("
        UPDATE tr_accounts
        SET    last_modified = '$now',
               first_name = '$first_name',
               last_name = '$last_name',
               email = '$email',
               password = '$password',
               ui_language_id = $ui_language_id,
               ui_num_data_per_page = $ui_num_data_per_page,
               receive_email_notifications = '$receive_email_notifications'
        WHERE  account_id = $account_id
          ") or ot_handle_error(mysql_error());

      $query = mysql_query("
        UPDATE tr_translators
        SET    translation_disclaimer = '$translation_disclaimer',
               default_bulk_translate_view = '$default_bulk_translate_view'
        WHERE  translator_id = $account_id
          ") or die(mysql_error());

      // update the translator languages
      mysql_query("DELETE FROM tr_translator_languages WHERE translator_id=$account_id");

      for ($i=0; $i<count($selected_languages); $i++)
        mysql_query("INSERT INTO tr_translator_languages (translator_id, language_id) VALUES ($account_id, {$selected_languages[$i]})");

		  // update sessions
			$_SESSION["ot"]["ui_num_data_per_page"] = $ui_num_data_per_page;
      $_SESSION["ot"]["bulk_translate_view"]  = $default_bulk_translate_view;
		  break;

    case "project_manager":
      $first_name = $info["first_name"];
      $last_name = $info["last_name"];
      $email    = $info["email"];
      $password = $info["password"];
      $ui_language_id = $info["ui_language_id"];
      $receive_email_notifications = $info["receive_email_notifications"];
      $can_create_projects = $info["can_create_projects"];
      $can_create_project_manager_accounts = $info["can_create_project_manager_accounts"];
      $can_create_translator_accounts = $info["can_create_translator_accounts"];
      $can_export_data = $info["can_export_data"];

      $query = mysql_query("
        UPDATE tr_accounts
        SET    last_modified = '$now',
               first_name = '$first_name',
               last_name = '$last_name',
               email = '$email',
               password = '$password',
               ui_language_id = $ui_language_id,
               receive_email_notifications = '$receive_email_notifications'
        WHERE  account_id = $account_id
          ") or die(mysql_error());

      $query = mysql_query("
        UPDATE tr_project_managers
        SET    can_create_projects = '$can_create_projects',
               can_create_project_manager_accounts = '$can_create_project_manager_accounts',
               can_create_translator_accounts = '$can_create_translator_accounts',
               can_export_data = '$can_export_data'
        WHERE  project_manager_id = $account_id
          ") or die(mysql_error());
      break;
  }

  return array(true, $LANG["text_account_updated"]);
}
