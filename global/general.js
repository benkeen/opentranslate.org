// assumes current_tab has been defined

function change_tab(tab)
{
  if (current_tab == tab)
    return;

  $('tab' + current_tab + '_content').hide();
  $('tab' + tab + '_content').show();
  $('data_tab' + tab).style.backgroundImage = 'url("' + g.root_url + '/images/tab_selected.jpg")';
  $('data_tab' + current_tab).style.backgroundImage = 'url("' + g.root_url + '/images/tab_unselected.jpg")';
  current_tab = tab;

  return false;
}

function addOption(theSel, theText, theValue)
{
  var newOpt = new Option(theText, theValue);
  var selLength = theSel.length;
  theSel.options[selLength] = newOpt;
}

function deleteOption(theSel, theIndex)
{
  var selLength = theSel.length;
  if(selLength>0)
  {
    theSel.options[theIndex] = null;
  }
}

function moveOptions(theSelFrom, theSelTo)
{
  var selLength = theSelFrom.length;
  var selectedText = new Array();
  var selectedValues = new Array();
  var selectedCount = 0;

  var i;

  // find the selected Options in reverse order and delete them from the 'from' Select
  for (i=selLength-1; i>=0; i--)
  {
    if (theSelFrom.options[i].selected)
    {
      // if there's no value, that means the lawyer is away. Don't move them.
      if (theSelFrom.options[i].value == "")
        continue;

      selectedText[selectedCount] = theSelFrom.options[i].text;
      selectedValues[selectedCount] = theSelFrom.options[i].value;
      deleteOption(theSelFrom, i);
      selectedCount++;
    }
  }

  // add the selected text/values in reverse order. This will add the Options to the 'to' Select
  // in the same order as they were in the 'from' Select
  for(i=selectedCount-1; i>=0; i--)
  {
    addOption(theSelTo, selectedText[i], selectedValues[i]);
  }
}


function checkSelected(element)
{
  // loop through each and select them (this passes them along to the server)
  for (i=0; i<element.length; i++)
    element[i].selected = true;
}


// main Ajax server request function
function httpRequest(reqType, url, asynch, responseHandle)
{
  // Mozilla-based browsers
  if (window.XMLHttpRequest)
    g_request = new XMLHttpRequest();
  else if (window.ActiveXObject)
  {
    g_request =  new ActiveXObject("Msxml2.XMLHTTP");
    if (!g_request)
      g_request =  new ActiveXObject("Microsoft.XMLHTTP");
  }

  // unlikely, but we test for a null request if neither ActiveXObject was initialized
  if (g_request)
  {
    // if the reqType parameter is POST, then the 5th argument to the function is the POSTed data
    if (reqType.toLowerCase() != "post")
      initReq(reqType, url, asynch, responseHandle)
    else
    {
      var args = arguments[4];
      if (args != null && args.length > 0)
        initReq(reqType, url, asynch, responseHandle, args);
    }
  }
  else
  {
    alert("Sorry, your browser looks pretty old, so this site won't function "
		    + "properly for you. Time to upgrade... - or send us a nasty note "
				+ "telling us there's a bug with the site.");
  }
}

function initReq(reqType, url, bool, responseHandle)
{
  try
  {
    // specify the function that will handle the HTTP response
    g_request.onreadystatechange = responseHandle;
    g_request.open(reqType, url, bool);

    if (reqType.toLowerCase() == "post")
    {
      g_request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset:UTF-8");
      g_request.send(arguments[4]);
    }
    else
    {
      g_request.send(null);
    }
  }
  catch (errv)
  {
	  // need to update
    alert("The application cannot contact the server at this moment. Please wait and try again.");
  }
}