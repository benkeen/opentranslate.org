<?php
require("GTranslate.php");

/**
* Example using RequestHTTP
*/
$translate_string = "Das ist wunderschön";
 try{
       $gt = new Gtranslate;
	echo "[HTTP] Translating [$translate_string] German to English => ".$gt->german_to_english($translate_string)."<br/>";

	/**
	* Lets switch the request type to CURL
	*/
	$gt->setRequestType('curl');

	echo "[CURL] Translating [$translate_string] German to English => ".$gt->german_to_english($translate_string)."<br/>";

} catch (GTranslateException $ge)
 {
       echo $ge->getMessage();
 }
