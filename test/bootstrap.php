<?php

require_once dirname(__FILE__).'/../lib/AdobeDigitalMarketing/Autoloader.php';
AdobeDigitalMarketing_Autoloader::register();

// autoload abstract classes (we need to add an autoloader for testing classes)
require_once (dirname(__file__).'/AdobeDigitalMarketing/Tests/Api/ApiTest.php');