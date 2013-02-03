/*------------------------------------------------------------------------------------------------*\
  File:     manage_lists.js
  Abstract: contains assorted list-related javascript functions used in the Open Translate UI.
\*------------------------------------------------------------------------------------------------*/


/*------------------------------------------------------------------------------------------------*\
  Function:   check_selected
  Purpose:    validation function called when the user clicks the "download" or "view" buttons for
              the Excel download and Printer-Friendly page. If the "selected" option is selected,
              it checks that the user has selected at least one submission.
\*------------------------------------------------------------------------------------------------*/
function check_selected(action, select_option)
{
  var selected_ids = get_selected_ids();

  if (select_option == "all")
    return true;

  if (!selected_ids.length)
  {
    if (action == "print_preview")
      alert("Please select those rows you would like to view.");
    else
      alert("Please select those rows you would like to download.");
    
    return false;
  }
  
  return true;
}
