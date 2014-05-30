<?php

use BBCParser\Parser;

require 'vendor/autoload.php';

header('Content-Type: text/html; charset=utf-8');

// create BBCParser instance, parse and serialize data
$parser = new Parser();
$parser->parseAndSerializeData();

// return and data for modules
$sport = $parser->returnDataForModule("Sport");
$weather = $parser->returnDataForModule("Weather");
Kint::dump($sport);
Kint::dump($weather);

?>