<?php

/*--------------------------------------------------------------------------------------------*\
  Function: validate_fields()
  Purpose:  generic form field validation.
  Parameters: field - the POST / GET fields from a form which need to be validated.
              rules - an array of the validation rules. Each rule is a string of the form:

   "[if:FIELDNAME=VALUE,]REQUIREMENT,fieldname[,fieldname2 [,fieldname3, date_flag]],error message"
  
              if:FIELDNAME=VALUE,   This allows us to only validate a field 
                          only if a fieldname FIELDNAME has a value VALUE. This 
                          option allows for nesting; i.e. you can have multiple 
                          if clauses, separated by a comma. They will be examined
                          in the order in which they appear in the line.

              Valid REQUIREMENT strings are: 
                "required"    - field must be filled in
                "digits_only" - field must contain digits only

                "length=X"    - field has to be X characters long
                "length=X-Y"  - field has to be between X and Y (inclusive) characters long
                "length>X"    - field has to be greater than X characters long
                "length>=X"   - field has to be greater than or equal to X characters long
                "length<X"    - field has to be less than X characters long
                "length<=X"   - field has to be less than or equal to X characters long

                "valid_email" - field has to be valid email address
                "valid_date"  - field has to be a valid date
                      fieldname:  MONTH 
                      fieldname2: DAY 
                      fieldname3: YEAR
                      date_flag:  "later_date" / "any_date"
                "same_as"     - fieldname is the same as fieldname2 (for password comparison)

                "range=X-Y"   - field must be a number between the range of X and Y inclusive
                "range>X"     - field must be a number greater than X
                "range>=X"    - field must be a number greater than or equal to X
                "range<X"     - field must be a number less than X
                "range<=X"    - field must be a number less than or equal to X

                "is_alpha"    - field must only contain alphanumeric characters (0-9, a-Z)
  
  Comments:   With both digits_only, valid_email and is_alpha options, if the empty string is passed 
              in it won't generate an error, thus allowing validation of non-required fields. So,
              for example, if you want a field to be a valid email address, provide validation for 
              both "required" and "valid_email".
\*--------------------------------------------------------------------------------------------*/
function validate_fields($fields, $rules)
{ 
  $errors = array();
  
  // loop through rules
  for ($i=0; $i<count($rules); $i++)
  {
    // split row into component parts 
    $row = split(",", $rules[$i]);
    
    // while the row begins with "if:..." test the condition. If true, strip the if:..., part and 
    // continue evaluating the rest of the line. Keep repeating this while the line begins with an 
    // if-condition. If it fails any of the conditions, don't bother validating the rest of the line
    $satisfies_if_conditions = true;
    while (preg_match("/^if:/", $row[0]))
    {
      $condition = preg_replace("/^if:/", "", $row[0]);
      $parts = split("=", $condition);
      $field_to_check = $parts[0];
      $value_to_check = $parts[1];

      // if the VALUE is NOT the same, we don't need to validate this field. Return.
      if ($fields[$field_to_check] != $value_to_check)
      {
        $satisfies_if_conditions = false;
        break;
      }
      else 
        array_shift($row);    // remove this if-condition from line, and continue validating line
    }

    if (!$satisfies_if_conditions)
      continue;


    $requirement = $row[0];
    $field_name  = $row[1];

    // depending on the validation test, store the incoming strings for use later...
    if (count($row) == 6)        // valid_date
    {
      $field_name2   = $row[2];
      $field_name3   = $row[3];
      $date_flag     = $row[4];
      $error_message = $row[5];
    }
    else if (count($row) == 4)     // same_as
    {
      $field_name2   = $row[2];
      $error_message = $row[3];
    }
    else
      $error_message = $row[2];    // everything else!


    // if the requirement is "length=...", rename requirement to "length" for switch statement
    if (preg_match("/^length/", $requirement))
    {
      $length_requirements = $requirement;
      $requirement         = "length";
    }

    // if the requirement is "range=...", rename requirement to "range" for switch statement
    if (preg_match("/^range/", $requirement))
    {
      $range_requirements = $requirement;
      $requirement        = "range";
    }


    // now, validate whatever is required of the field
    switch ($requirement)
    {
      case "required":
        if (!isset($fields[$field_name]) || $fields[$field_name] == "")
          $errors[] = $error_message;
        break;

      case "digits_only":       
        if (isset($fields[$field_name]) && preg_match("/\D/", $fields[$field_name]))
          $errors[] = $error_message;
        break;

      // doesn't fail if field is empty
      case "valid_email":
        $regexp="/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";     
        if (isset($fields[$field_name]) && !preg_match($regexp, $fields[$field_name]))
          $errors[] = $error_message;
        break;

      case "length":
        $comparison_rule = "";
        $rule_string     = "";

        if      (preg_match("/length=/", $length_requirements))
        {
          $comparison_rule = "equal";
          $rule_string = preg_replace("/length=/", "", $length_requirements);
        }
        else if (preg_match("/length>=/", $length_requirements))
        {
          $comparison_rule = "greater_than_or_equal";
          $rule_string = preg_replace("/length>=/", "", $length_requirements);
        }
        else if (preg_match("/length<=/", $length_requirements))
        {
          $comparison_rule = "less_than_or_equal";
          $rule_string = preg_replace("/length<=/", "", $length_requirements);
        }
        else if (preg_match("/length>/", $length_requirements))
        {
          $comparison_rule = "greater_than";
          $rule_string = preg_replace("/length>/", "", $length_requirements);
        }
        else if (preg_match("/length</", $length_requirements))
        {
          $comparison_rule = "less_than";
          $rule_string = preg_replace("/length</", "", $length_requirements);
        }

        switch ($comparison_rule)
        {
          case "greater_than_or_equal":
            if (!(strlen($fields[$field_name]) >= $rule_string))
              $errors[] = $error_message;
            break;
          case "less_than_or_equal":
            if (!(strlen($fields[$field_name]) <= $rule_string))
              $errors[] = $error_message;
            break;
          case "greater_than":
            if (!(strlen($fields[$field_name]) > $rule_string))
              $errors[] = $error_message;
            break;
          case "less_than":
            if (!(strlen($fields[$field_name]) < $rule_string))
              $errors[] = $error_message;
            break;
          case "equal":
            // if the user supplied two length fields, make sure the field is within that range
            if (preg_match("/-/", $rule_string))
            {
              list($start, $end) = split("-", $rule_string);
              if (strlen($fields[$field_name]) < $start || strlen($fields[$field_name]) > $end)
                $errors[] = $error_message;
            }
            // otherwise, check it's EXACTLY the size the user specified 
            else
            {
              if (strlen($fields[$field_name]) != $rule_string)
                $errors[] = $error_message;
            }     
            break;       
        }
        break;

      case "range":
        $comparison_rule = "";
        $rule_string     = "";

        if      (preg_match("/range=/", $range_requirements))
        {
          $comparison_rule = "equal";
          $rule_string = preg_replace("/range=/", "", $range_requirements);
        }
        else if (preg_match("/range>=/", $range_requirements))
        {
          $comparison_rule = "greater_than_or_equal";
          $rule_string = preg_replace("/range>=/", "", $range_requirements);
        }
        else if (preg_match("/range<=/", $range_requirements))
        {
          $comparison_rule = "less_than_or_equal";
          $rule_string = preg_replace("/range<=/", "", $range_requirements);
        }
        else if (preg_match("/range>/", $range_requirements))
        {
          $comparison_rule = "greater_than";
          $rule_string = preg_replace("/range>/", "", $range_requirements);
        }
        else if (preg_match("/range</", $range_requirements))
        {
          $comparison_rule = "less_than";
          $rule_string = preg_replace("/range</", "", $range_requirements);
        }
        
        switch ($comparison_rule)
        {
          case "greater_than":
            if (!($fields[$field_name] > $rule_string))
              $errors[] = $error_message;
            break;
          case "less_than":
            if (!($fields[$field_name] < $rule_string))
              $errors[] = $error_message;
            break;
          case "greater_than_or_equal":
            if (!($fields[$field_name] >= $rule_string))
              $errors[] = $error_message;
            break;
          case "less_than_or_equal":
            if (!($fields[$field_name] <= $rule_string))
              $errors[] = $error_message;
            break;
          case "equal":
            list($start, $end) = split("-", $rule_string);

            if (($fields[$field_name] < $start) || ($fields[$field_name] > $end))
              $errors[] = $error_message;
            break;
        }
        break;
        
      case "same_as":
        if ($fields[$field_name] != $fields[$field_name2])
          $errors[] = $error_message;
        break;

      case "valid_date":
        // this is written for future extensibility of isValidDate function to allow 
        // checking for dates BEFORE today, AFTER today, IS today and ANY day.
        $is_later_date = false;
        if    ($date_flag == "later_date")
          $is_later_date = true;
        else if ($date_flag == "any_date")
          $is_later_date = false;

        if (!is_valid_date($fields[$field_name], $fields[$field_name2], $fields[$field_name3], $is_later_date))
          $errors[] = $error_message;
        break;

      case "is_alpha":
        if (preg_match('/[^A-Za-z0-9]/', $fields[$field_name]))
          $errors[] = $error_message; 
        break;
        
      default:
        die("Unknown requirement flag in validateFields(): $requirement");
        break;
    }
  }
  
  return $errors;
}


/*------------------------------------------------------------------------------------------------*\
  Function:   is_valid_date
  Purpose:    checks a date is valid / is later than current date
  Parameters: $month       - an integer between 1 and 12
              $day         - an integer between 1 and 31 (depending on month)
              $year        - a 4-digit integer value
              $is_later_date - a boolean value. If true, the function verifies the date being passed 
                               in is LATER than the current date.
\*------------------------------------------------------------------------------------------------*/
function is_valid_date($month, $day, $year, $is_later_date)
{
  // depending on the year, calculate the number of days in the month
  if ($year % 4 == 0)      // LEAP YEAR 
    $days_in_month = array(31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
  else
    $days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);


  // first, check the incoming month and year are valid. 
  if (!$month || !$day || !$year) return false;
  if (1 > $month || $month > 12)  return false;
  if ($year < 0)                  return false;
  if (1 > $day || $day > $days_in_month[$month-1]) return false;


  // if required, verify the incoming date is LATER than the current date.
  if ($is_later_date)
  {    
    // get current date
    $today = date("U");
    $date = mktime(0, 0, 0, $month, $day, $year);
    if ($date < $today)
      return false;
  }

  return true;
}

?>
