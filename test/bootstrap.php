<?php

require_once __DIR__.'/../vendor/autoload.php';

// autoload abstract classes (we need to add an autoloader for testing classes)
require_once (dirname(__FILE__).'/AdobeMarketingCloud/Tests/BaseTestCase.php');

if (file_exists(dirname(__FILE__).'/credentials.php')) {
    require_once(dirname(__FILE__).'/credentials.php');
}