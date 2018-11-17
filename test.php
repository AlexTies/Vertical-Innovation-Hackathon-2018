<?php

//units=For temperature in Celsius use units=metric
//5128638 is new york ID
require_once 'Classes/Pic.php';
require_once 'Classes/GetContent.php';

$content= new GetContent();
$content->getContentFromWeb();
echo '<script>';
$content->convertToJSArray();
echo '</script>';


?>