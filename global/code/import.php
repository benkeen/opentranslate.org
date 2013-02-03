<?php

/*------------------------------------------------------------------------------------------------*\
  Copyright (C) 2010 Benjamin Keen
  http://www.opentranslate.org
\*------------------------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------------------------*\
  IMPORT - functions in brief

	  ot_get_php_import_file_vars
    _ot_get_defined_file_vars

\*------------------------------------------------------------------------------------------------*/


/**
 * Imports data from a PHP file containing one or more associative arrays.
 */
function ot_get_php_import_file_vars($project_id, $version_id, $file_info)
{
  global $g_root_dir, $LANG;

  // check this is a PHP file
  if ($file_info["type"] != "text/php" && $file_info["type"] != "text/plain" && $file_info["type"] != "application/x-php" &&
    $file_info["type"] != "application/octet-stream")
  {
  	return array(false, $LANG["validation_not_php_file"]);
  }

  // move the file to the tmp folder and name it something unique
  $tmp_filename = $file_info["tmp_name"];
  $unique_filename = "tmpfile" . date("U") . ".php";

  $new_file = "$g_root_dir/tmp/$unique_filename";
  copy($tmp_filename, $new_file);
  chmod($new_file, 0777);

  $vars = _ot_get_defined_file_vars($new_file);

  // delete the tmp file
  unlink($new_file);

  if (!empty($vars))
    return array(true, $vars);
  else
    return array(false, $LANG["validation_no_php_lang_vars_found"]);
}


/**
 * Helper function called by php_file_import which provides a scope for the contents of an uploaded
 * PHP file. This function returns all hashes within the file.
 */
function _ot_get_defined_file_vars($file)
{
  include($file);

  $defined_vars = get_defined_vars();

  $possible_lang_vars = array();
  foreach ($defined_vars as $var)
  {
    if (ot_is_hash($var))
      $possible_lang_vars[] = $var;
  }

  return $possible_lang_vars;
}
