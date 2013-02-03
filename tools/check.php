<?php

$lines = file("en_us.php");

//print_r($lines);

$vars = array();

foreach ($lines as $line)
{
  if (preg_match("/^\\\$LANG\[\"(.*)\"\]/", $line, $match))
  {
    $key = $match[1];
    $vars[] = $key;
  }
}

sort($vars);
//$vars = array_unique($vars);


print_r(array_repeated($vars));


function array_repeated($array) {

    if(!is_array($array)) return false;
    $repeated_values = Array();
    $array_unique = array_unique($array);
    if(count($array)-count($array_unique)) {
        for($i=0;$i<count($array);$i++) {
            if(!array_key_exists($i, $array_unique)) $repeated_values[] = $array[$i];
        }
    }
    return $repeated_values;
}


?>