<?php
require("GTranslate.php");
$translate_string = "Das ist wunderschön";
 try{
       $gt = new Gtranslate;
	$gt->setRequestType('curl');
	echo "Translating [$translate_string] German to English => ".$gt->german_to_english($translate_string)."<br/>";

} catch (GTranslateException $ge)
 {
       echo $ge->getMessage();
 }
