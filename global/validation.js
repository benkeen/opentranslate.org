/*--------------------------------------------------------------------------------------------*\

  validation.js
  -------------

  v2.0, Dec 2006

  This script provides generic validation for any web form. For a discussion and example usage 
  of this script, go to http://www.benjaminkeen.com/software/js_validation

  This script is written by Ben Keen with additional code contributed by Mihai Ionescu and 
  Nathan Howard. It is free to distribute, to re-write - to do what ever you want with it.

  Before using it, please read the following disclaimer.

  THIS SOFTWARE IS PROVIDED ON AN "AS-IS" BASIS WITHOUT WARRANTY OF ANY KIND. BENJAMINKEEN.COM 
  SPECIFICALLY DISCLAIMS ANY OTHER WARRANTY, EXPRESS OR IMPLIED, INCLUDING ANY WARRANTY OF 
  MERCHANTABILITY OR FITNESS FOR A PARTICULAR PURPOSE. IN NO EVENT SHALL BENJAMINKEEN.COM BE 
  LIABLE FOR ANY CONSEQUENTIAL, INDIRECT, SPECIAL OR INCIDENTAL DAMAGES, EVEN IF BENJAMINKEEN.COM 
  HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH POTENTIAL LOSS OR DAMAGE. USER AGREES TO HOLD 
  BENJAMINKEEN.COM HARMLESS FROM AND AGAINST ANY AND ALL CLAIMS, LOSSES, LIABILITIES AND EXPENSES.

\*--------------------------------------------------------------------------------------------*/


/*--------------------------------------------------------------------------------------------*\
  Function: validateFields()
  Purpose:  generic form field validation.
  Parameters: form  - the name of the form to validate
              rules - an array of the validation rules. Each rule is a string of the form:

   "[if:FIELDNAME=VALUE,]REQUIREMENT,fieldname[,fieldname2 [,fieldname3, date_flag]],error message"
  
              if:FIELDNAME=VALUE,   This allows us to only validate a field only if a fieldname 
                       FIELDNAME has a value VALUE. This option allows for nesting; i.e. you can 
                       have multiple if clauses, separated by a comma. They will be examined in the 
                       order in which they appear in the line.

              Valid REQUIREMENT strings are: 
                "required"    - field must be filled in
                "digits_only" - field must contain digits only

                "length=X"    - field has to be X characters long
                "length=X-Y"  - field has to be between X and Y (inclusive) characters long
                "length>X"    - field has to be greater than X characters long
                "length>=X"   - field has to be greater than or equal to X characters long
                "length<X"    - field has to be less than X characters long
                "length<=X"   - field has to be less than or equal to X characters long
                
                "valid_email" - field has to be a valid email address
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
\*------------------------------------------------------------------------------------------------*/
function validateFields(form, rules)
{
  // loop through rules
  for (var i=0; i<rules.length; i++)
  {
    // split row into component parts 
    var row = rules[i].split(",");

    // while the row begins with "if:..." test the condition. If true, strip the
    // if:..., part and continue evaluating the rest of the line. Keep repeating 
    // this while the line begins with an if-condition. If it fails any of the 
    // conditions, don't bother validating the rest of the line.
    var satisfiesIfConditions = true;
    while (row[0].match("^if:"))
    {
      var condition = row[0];
      condition = condition.replace("if:", "");
      var parts = condition.split("=");
      var fieldToCheck = parts[0];
      var valueToCheck = parts[1];

      // find value of FIELDNAME for conditional check
      var fieldnameValue = "";
      if (form[fieldToCheck].type == undefined) // RADIO
      {
        for (var j=0; j<form[fieldToCheck].length; j++)
        {
          if (form[fieldToCheck][j].checked)
            fieldnameValue = form[fieldToCheck][j].value;
        }
      }
      // single checkbox      
      else if (form[fieldToCheck].type == "checkbox")
      {
        if (form[fieldToCheck].checked)
          fieldnameValue = form[parts[0]].value;
      }      
      // all other field types
      else
        fieldnameValue = form[parts[0]].value;

      // if the VALUE is NOT the same, we don't need to validate this field. Return.
      if (fieldnameValue != valueToCheck)
      {
        satisfiesIfConditions = false;
        break;
      }
      else
        row.shift();    // remove this if-condition from line, and continue validating line
    }

    if (!satisfiesIfConditions)
      continue;


    var requirement = row[0];
    var fieldName   = row[1];

    // depending on the validation test, store the incoming strings for use later...
    if (row.length == 6)        // valid_date
    {
      var fieldName2   = row[2];
      var fieldName3   = row[3];
      var date_flag    = row[4];
      var errorMessage = row[5];
    }
    else if (row.length == 4)     // same_as
    {
      var fieldName2   = row[2];
      var errorMessage = row[3];
    }
    else
      var errorMessage = row[2];    // everything else!


    // if the requirement is "length...", rename requirement to "length" for switch statement
    if (requirement.match("^length"))
    {
      var lengthRequirements = requirement;
      requirement = "length";
    }

    // if the requirement is "range=...", rename requirement to "range" for switch statement
    if (requirement.match("^range"))
    {
      var rangeRequirements = requirement;
      requirement = "range";
    }


    // now, validate whatever is required of the field
    switch (requirement)
    {
      case "required":
     
        // if radio buttons or multiple checkboxes:
        if (form[fieldName].type == undefined)
        {
          var oneIsChecked = false;
          for (var j=0; j<form[fieldName].length; j++)
          {
            if (form[fieldName][j].checked)
              oneIsChecked = true;
          }
          if (!oneIsChecked)
          {
            alertMessage(form[fieldName], errorMessage);
            return false;           
          }
        }
        else if (form[fieldName].type == "select-multiple")
        {          
          var oneIsSelected = false;
          for (k=0; k<form[fieldName].length; k++)
          {
            if (form[fieldName][k].selected)
              oneIsSelected = true;
          }

          // if no options have been selected, or if there ARE no options in the multi-select 
          // dropdown, return false
          if (!oneIsSelected || form[fieldName].length == 0)
          {
            alertMessage(form[fieldName], errorMessage);
            return false;          
          }
        }
        // a single checkbox
        else if (form[fieldName].type == "checkbox")
        {
          if (!form[fieldName].checked)
          {
            alertMessage(form[fieldName], errorMessage);
            return false;           
          }
        }        
        // otherwise, just perform ordinary "required" check.
        else if (!form[fieldName].value)
        {
          alertMessage(form[fieldName], errorMessage);
          return false;
        }
        break;

      case "digits_only":       
        if (form[fieldName].value && form[fieldName].value.match(/\D/))
        {
          alertMessage(form[fieldName], errorMessage);
          return false;
        }
        break;

      case "is_alpha": 
        if (form[fieldName].value && form[fieldName].value.match(/\W/))
        {
          alertMessage(form[fieldName], errorMessage);
          return false;
        }
        break;

      case "length":

        comparison_rule = "";
        rule_string = "";

        // if-else order is important here: needs to check for >= before >
        if      (lengthRequirements.match(/length=/))
        { 
          comparison_rule = "equal"; 
          rule_string = lengthRequirements.replace("length=", ""); 
        }
        else if (lengthRequirements.match(/length>=/))
        {
          comparison_rule = "greater_than_or_equal"; 
          rule_string = lengthRequirements.replace("length>=", "");
        }
        else if (lengthRequirements.match(/length>/))
        {
          comparison_rule = "greater_than"; 
          rule_string = lengthRequirements.replace("length>", "");
        }
        else if (lengthRequirements.match(/length<=/))
        {
          comparison_rule = "less_than_or_equal"; 
          rule_string = lengthRequirements.replace("length<=", "");
        }        
        else if (lengthRequirements.match(/length</))
        {
          comparison_rule = "less_than"; 
          rule_string = lengthRequirements.replace("length<", "");
        }

        // now perform the appropriate validation
        switch (comparison_rule)
        {
          case "greater_than_or_equal":
            if (!(form[fieldName].value.length >= parseInt(rule_string)))
            {
              alertMessage(form[fieldName], errorMessage);
              return false;
            }
            break;

          case "greater_than":
            if (!(form[fieldName].value.length > parseInt(rule_string)))
            {
              alertMessage(form[fieldName], errorMessage);
              return false;
            }
            break;

          case "less_than_or_equal":
            if (!(form[fieldName].value.length <= parseInt(rule_string)))
            {
              alertMessage(form[fieldName], errorMessage);
              return false;
            }
            break;

          case "less_than":
            if (!(form[fieldName].value.length < parseInt(rule_string)))
            {
              alertMessage(form[fieldName], errorMessage);
              return false;
            }
            break;

          case "equal":
            var range_or_exact_number = rule_string.match(/[^_]+/);
            var fieldCount = range_or_exact_number[0].split("-");
    
            // if the user supplied two length fields, make sure the field is within that range
            if (fieldCount.length == 2)
            {
              if (form[fieldName].value.length < fieldCount[0] || form[fieldName].value.length > fieldCount[1])
              {
                alertMessage(form[fieldName], errorMessage);
                return false;
              }
            }
    
            // otherwise, check it's EXACTLY the size the user specified 
            else
            {
              if (form[fieldName].value.length != fieldCount[0])
              {
                alertMessage(form[fieldName], errorMessage);
                return false;
              }
            }     

            break;
        }
        break;

      // this is also true if field is empty [should be same for digits_only]
      case "valid_email":
        if (form[fieldName].value && !isValidEmail(form[fieldName].value))
        {
          alertMessage(form[fieldName], errorMessage);
          return false;         
        }
        break;

      case "valid_date":

        // this is written for future extensibility of isValidDate function to allow 
        // checking for dates BEFORE today, AFTER today, IS today and ANY day.
        var isLaterDate = false;
        if    (date_flag == "later_date")
          isLaterDate = true;
        else if (date_flag == "any_date")
          isLaterDate = false;

        if (!isValidDate(form[fieldName].value, form[fieldName2].value, form[fieldName3].value, isLaterDate))
        {
          alertMessage(form[fieldName], errorMessage);
          return false;
        }
        break;

      case "same_as":
        if (form[fieldName].value != form[fieldName2].value)
        {
          alertMessage(form[fieldName], errorMessage);
          return false;
        }       
        break;

      case "range":
     
        comparison_rule = "";
        rule_string = "";

        // if-else order is important here: needs to check for >= before >
        if      (rangeRequirements.match(/range=/))
        { 
          comparison_rule = "equal";
          rule_string = rangeRequirements.replace("range=", ""); 
        }
        else if (rangeRequirements.match(/range>=/))
        {
          comparison_rule = "greater_than_or_equal";
          rule_string = rangeRequirements.replace("range>=", "");
        }
        else if (rangeRequirements.match(/range>/))
        {
          comparison_rule = "greater_than";
          rule_string = rangeRequirements.replace("range>", "");
        }
        else if (rangeRequirements.match(/range<=/))
        {
          comparison_rule = "less_than_or_equal";
          rule_string = rangeRequirements.replace("range<=", "");
        }        
        else if (rangeRequirements.match(/range</))
        {
          comparison_rule = "less_than";
          rule_string = rangeRequirements.replace("range<", "");
        }

        // now perform the appropriate validation
        switch (comparison_rule)
        {
          case "greater_than_or_equal":
            if (!(form[fieldName].value >= Number(rule_string)))
            {
              alertMessage(form[fieldName], errorMessage);
              return false;
            }
            break;

          case "greater_than":
            if (!(form[fieldName].value > Number(rule_string)))
            {
              alertMessage(form[fieldName], errorMessage);
              return false;
            }
            break;

          case "less_than_or_equal":
            if (!(form[fieldName].value <= Number(rule_string)))
            {
              alertMessage(form[fieldName], errorMessage);
              return false;
            }
            break;

          case "less_than":
            if (!(form[fieldName].value < Number(rule_string)))
            {
              alertMessage(form[fieldName], errorMessage);
              return false;
            }
            break;

          case "equal":
            var rangeValues = rule_string.split("-");
            
            // if the user supplied two length fields, make sure the field is within that range
            if ((form[fieldName].value < Number(rangeValues[0])) || (form[fieldName].value > Number(rangeValues[1])))
            {
              alertMessage(form[fieldName], errorMessage);
              return false;
            }
            break;
       }
        break;

      default:
        alert("Unknown requirement flag in validateFields(): " + requirement);
        return false;
    }
  }
  
  return true;
}


/*--------------------------------------------------------------------------------------------*\
  Function: alertMessage()
  Purpose:  simple helper function which alerts a message, then focuses on and highlights 
            a particular field.
\*--------------------------------------------------------------------------------------------*/
function alertMessage(obj, message)
{ 
  var backgroundColor = "#F2F9FF";

  alert(message);

  // if "obj" is an array: it's a radio button. Focus on the first element.
  if (obj.type == undefined)
    obj[0].focus();
  else
  {
    obj.style.background = backgroundColor;
    obj.focus();
  }
  return false;
}


/*--------------------------------------------------------------------------------------------*\
  Function: isValidEmail
  Purpose:  tests a string is a valid email
\*--------------------------------------------------------------------------------------------*/
function isValidEmail(str)
{
  // trim starting / ending whitespace
  str = str.replace(/^\s*/, "");
  str = str.replace(/\s*$/, "");

  var at="@"
  var dot="."
  var lat=str.indexOf(at)
  var lstr=str.length
  var ldot=str.indexOf(dot)

  if (str.indexOf(at)==-1)
    return false
  
  if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr)
    return false
  
  if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr)
    return false

  if (str.indexOf(at,(lat+1))!=-1)
    return false

  if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot)
    return false

  if (str.indexOf(dot,(lat+2))==-1)
    return false

  if (str.indexOf(" ")!=-1)
    return false

  return true;
}


// helper function to check to see if a string is empty
function isEmpty(str)
{  
  return ((str == null) || (str.length == 0));
}


/*--------------------------------------------------------------------------------------------*\
  Function: isWhitespace()
  Purpose:  Returns true if string parameter is empty or whitespace characters only.
\*--------------------------------------------------------------------------------------------*/
function isWhitespace(s)
{
  var i;

  // Is s empty?
  if (isEmpty(s)) return true;

  for (var i=0; i<s.length; i++)
  {   
    var c = s.charAt(i);
    if (whitespace.indexOf(c) == -1)
      return false;
  }

  return true;
}


/*----------------------------------------------------------------------------*\
  Function:   isValidDate()
  Purpose:    to check an incoming date is valid. If any of the date parameters  
              fail, it returns a string message denoting the problem.
  Parameters: month       - an integer between 1 and 12
              day         - an integer between 1 and 31 (depending on month)
              year        - a 4-digit integer value
              isLaterDate - a boolean value. If true, the function verifies the 
                            date being passed in is LATER than the current date.
\*----------------------------------------------------------------------------*/
function isValidDate(month, day, year, isLaterDate)
{
  // depending on the year, calculate the number of days in the month
  if (year % 4 == 0)      // LEAP YEAR 
    var daysInMonth = new Array(31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
  else
    var daysInMonth = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);


  // first, check the incoming month and year are valid. 
  if (!month || !day || !year)          return false;
  if (1 > month || month > 12)          return false;
  if (year < 0)                         return false;
  if (1 > day || day > daysInMonth[month-1]) return false;


  // if required, verify the incoming date is LATER than the current date.
  if (isLaterDate)
  {
    // get current date
    var today = new Date();
    var currMonth = today.getMonth() + 1; // since returns 0-11
    var currDay   = today.getDate();
    var currYear  = today.getFullYear();

    // zero-pad today's month & day
    if (String(currMonth).length == 1)  currMonth = "0" + currMonth;
    if (String(currDay).length == 1)  currDay   = "0" + currDay;    
    currDate = String(currYear) + String(currMonth) + String(currDay);
    
    // zero-pad incoming month & day
    if (String(month).length == 1)  month = "0" + month;
    if (String(day).length == 1)  day   = "0" + day;
    incomingDate = String(year) + String(month) + String(day);

    if (Number(currDate) > Number(incomingDate))
      return false;
  }
  
  return true;
}
