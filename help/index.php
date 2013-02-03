<?php
session_start();
header("Cache-control: private");

require("../global/library.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <?php
  $g_page_title = "";
  require("$g_root_dir/global/header_code.php");
  ?>
</head>
<body>

<?php
$g_breadcrumb = array(
                  array($LANG["word_dashboard"], "$g_root_url/admin/"),
                  array($LANG["label_help_documentation"], ""),
                    );
require("$g_root_dir/global/templates/open_page.php");
?>

  <h1>Help Documentation (English)</h1>

  <p>
    Sections: Project Managers, Translators.
  </p>

  <h3>Project Versions</h3>

  <p>
    What are project versions?<br/>
    Why would I need to use them?<br />
    How to use versioning wisely
  </p>

  <h3>Data</h3>
  <p>
    Suggestions on how to split up your text for translation.
  </p>

  <h3>Download Formats</h3>

	<p>
	  For your convenience, Open Translate allows you to export the original text and
		translations in an assortment of ways and formats.
	</p>

  <h3>User Accounts</h3>
	<p>
	  Project Managers<br />
	  Translators<br />
	</p>

  <hr size="1" />

  <p class="heading_3">Form Tools-related (move to formtools.org as well)</p>
  - what do I do with the downloaded file?<br />
  - what if I download a translation that isn't 100% translated?<br />
  - how can I help translate?<br />
  - how reliable are the translations?<br />
  - requesting Form Tools in a new language<br />
  - can I download multiple translations?<br />
  <br />
  <b>Translators</b><br />
  - login<Br/>
  - sign up<br/>


<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
