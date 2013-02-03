<?php

/*------------------------------------------------------------------------------------------------*\
  Copyright (C) 2010 Benjamin Keen
  http://www.opentranslate.org
\*------------------------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------------------------*\
  PROJECT MANAGERS - functions in brief

    ot_add_project_manager
    ot_get_project_manager
    ot_get_project_managers
    ot_update_project_manager
    ot_delete_project_manager

\*------------------------------------------------------------------------------------------------*/


/**
 * Called by administrators / project managers to create a new project manager account. It logs who
 * creates the account.
 */
function ot_add_project_manager($info)
{
  global $LANG;

  $form_vals = ot_clean_hash($info);

  // validate POST fields
  $rules = array();
  $rules[] = "required,first_name,{$LANG['validation_message_no_first_name']}";
  $rules[] = "required,last_name,{$LANG['validation_message_no_last_name']}";
  $rules[] = "required,email,{$LANG['validation_message_no_email']}";
  $rules[] = "valid_email,email,{$LANG['validation_message_invalid_email']}";
  $rules[] = "required,password,{$LANG['validation_message_no_password']}";
  $rules[] = "required,ui_language_id,{$LANG['validation_message_no_ui_language_id']}";
  $errors = validate_fields($form_vals, $rules);

  if (!empty($errors))
  {
    $success = false;
    array_walk($errors, create_function('&$el','$el = "&bull;&nbsp; " . $el;'));
    $message = join("<br />", $errors);
    return array($success, $message);
  }

  // all checks out, add the new project manager account
  $created_by_account_id = $_SESSION["ot"]["account_id"];

  $now = ot_get_current_datetime();

  $first_name = $form_vals['first_name'];
  $last_name  = $form_vals['last_name'];
  $email      = $form_vals['email'];
  $password   = $form_vals['password'];
  $ui_language_id = $form_vals['ui_language_id'];
  $can_create_projects = $form_vals['can_create_projects'];
  $can_create_project_manager_accounts = $form_vals['can_create_project_manager_accounts'];
  $can_create_translator_accounts = $form_vals['can_create_translator_accounts'];
  $can_export_data = $form_vals['can_export_data'];


  // first, create the main ACCOUNT record
  $query = mysql_query("
		INSERT INTO tr_accounts (account_type, status, date_created, created_by_account_id, last_modified,
      last_logged_in, first_name, last_name, email, password, ui_language_id)
		VALUES ('project_manager', 'active', '$now', '$created_by_account_id', '$now', '', '$first_name',
      '$last_name', '$email', '$password', '$ui_language_id')
      ");

  $account_id = mysql_insert_id();

  // next, create a corresponding entry in the project_managers table
  $query = mysql_query("
		INSERT INTO tr_project_managers (project_manager_id, can_create_projects,
		  can_create_project_manager_accounts, can_create_translator_accounts, can_export_data)
		VALUES ($account_id, '$can_create_projects', '$can_create_project_manager_accounts',
		  '$can_create_translator_accounts', '$can_export_data')
      ");

  return array(true, $LANG["project_manager_account_created"]);
}


/**
 * Gets all information about a project managers
 */
function ot_get_project_manager($account_id)
{
  $query = mysql_query("
    SELECT *
		FROM   tr_accounts a, tr_project_managers pm
		WHERE  a.account_id = pm.project_manager_id AND
		       pm.project_manager_id = $account_id
	    ");

	$infohash = array();
	while ($field = mysql_fetch_assoc($query))
    $infohash = $field;

  // get the projects associated with this user
  $infohash["projects"] = array();
  $account_id = $infohash["account_id"];
  $project_manager_projects_query = mysql_query("
    SELECT p.project_id, p.name
    FROM   tr_projects p, tr_project_manager_projects pmp
    WHERE  p.project_id = pmp.project_id
    AND    pmp.account_id = $account_id
      ") or die(mysql_error());

  while ($project_info = mysql_fetch_assoc($project_manager_projects_query))
    $infohash["projects"][] = $project_info;

	return $infohash;
}


/**
 * Retrieves all project manager information. Used by administrators to get the full project
 * manager list.
 *
 * @param integer $project_id - if set, only returns those project managers that are assigned to
 *        this project.
 */
function ot_get_project_managers($project_id = "")
{
  if (!empty($project_id))
  {
    $query = mysql_query("
      SELECT *
  		FROM   tr_accounts a, tr_project_managers pm, tr_project_manager_projects pmp
  		WHERE  a.account_id = pm.project_manager_id AND
             pm.project_manager_id = pmp.account_id AND
             pmp.project_id = $project_id
      ORDER BY a.last_name
  	    ");
  }
  else
  {
    $query = mysql_query("
      SELECT *
  		FROM   tr_accounts a, tr_project_managers pm
  		WHERE  a.account_id = pm.project_manager_id
      ORDER BY a.last_name
  	    ");
  }

  // get the projects associated with each user
	$infohash = array();
	while ($field = mysql_fetch_assoc($query))
  {
    $tmphash = $field;
    $tmphash["projects"] = array();
    $account_id = $tmphash["account_id"];
    $project_manager_projects_query = mysql_query("
      SELECT p.project_id, p.name
      FROM   tr_projects p, tr_project_manager_projects pmp
      WHERE  p.project_id = pmp.project_id
      AND    pmp.account_id = $account_id
        ") or die(mysql_error());

    while ($project_info = mysql_fetch_assoc($project_manager_projects_query))
      $tmphash["projects"][] = $project_info;

    $infohash[] = $tmphash;
  }

	return $infohash;
}


/**
 * Updates a project manager account
 */
function ot_update_project_manager($info)
{
  $account_id = $info["account_id"];
  $first_name = $info["first_name"];
  $last_name  = $info["last_name"];
  $email      = $info["email"];
  $password   = $info["password"];
  $ui_language_id = $info["ui_language_id"];

	// projects
  $selected_projects = isset($_POST['selected_projects']) ? $_POST['selected_projects'] : "";

  // permissions
  $can_create_projects = $info["can_create_projects"];
  $can_create_project_manager_accounts = $info["can_create_project_manager_accounts"];
  $can_create_translator_accounts = $info["can_create_translator_accounts"];
  $can_export_data = $info["can_export_data"];

  $query = mysql_query("
    UPDATE tr_accounts a, tr_project_managers pm
    SET    a.first_name = '$first_name',
           a.last_name = '$last_name',
           a.email = '$email',
           a.password = '$password',
           a.ui_language_id = '$ui_language_id',
           pm.can_create_projects = '$can_create_projects',
           pm.can_create_project_manager_accounts = '$can_create_project_manager_accounts',
           pm.can_create_translator_accounts = '$can_create_translator_accounts',
           pm.can_export_data = '$can_export_data'
		WHERE  a.account_id = pm.project_manager_id
    AND    a.account_id = $account_id
	    ") or die(mysql_error());


	// delete all projects assigned to this user, and update them based on the new
	// settings.
	$query = mysql_query("
	  DELETE FROM tr_project_manager_projects
		WHERE       account_id = $account_id
        ");

	if (is_array($selected_projects))
	{
	  foreach ($selected_projects as $project_id)
		{
 			mysql_query("
 			  INSERT INTO tr_project_manager_projects (project_id, account_id)
			  VALUES ($project_id, $account_id)
          ") or die(mysql_error());
		}
	}
}


/**
 * Deletes a project manager account
 */
function ot_delete_project_manager($account_id)
{
  mysql_query("DELETE FROM tr_project_manager_projects WHERE account_id = $account_id");
  mysql_query("DELETE FROM tr_project_managers WHERE account_id = $account_id");
  mysql_query("DELETE FROM tr_accounts WHERE account_id = $account_id");
}
