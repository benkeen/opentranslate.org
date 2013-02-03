<?php

require("../../global/library.php");
$success = "";
$message = "";
$request = array_merge($_POST, $_GET);
$translator_id = $request["translator_id"];

if (isset($_POST['delete_translator']))
{
  ot_delete_translator($translator_id);

  header("location: index.php");
  exit;
}

$languages  = ot_get_languages();
$translator = ot_get_translator($translator_id);
