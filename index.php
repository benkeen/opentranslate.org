<?php
session_start();
header("Cache-control: private");
header('Content-Type: text/html; charset=utf-8');

require("global/library.php");
?>
<html>
<head>
  <?php
  $g_page_title = "";
  require("$g_root_dir/global/header_code.php");
  ?>
</head>
<body>

	<?php
	require("$g_root_dir/global/templates/open_page.php");
	?>

  <h1>Open Translate</h1>

  <p>
    Open Translate is currently in <b>Beta</b> and being used to translate the
    Open Source <a href="http://www.formtools.org">Form Tools</a> project. Please
    <a href="http://www.formtools.org/contact.php?subject=volunteer_translate">contact us</a> if you
    would be interested in helping translate Form Tools.
  </p>

<?php
require("$g_root_dir/global/templates/close_page.php");
?>

</body>
</html>
