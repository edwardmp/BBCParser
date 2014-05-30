<?php

header('Content-Type: text/html; charset=utf-8');

require 'vendor/autoload.php';

include('BBCParser.php');

// create BBCParser instance, parse and serialize data
$parser = new BBCParser();
+Kint::dump($parser->parseAndSerializeData());

/*
// return and data for modules
$sport = $parser->returnDataForModule("Sport");
$weather = $parser->returnDataForModule("Weather");
print_r($sport);
print_r($weather);
*/
?>