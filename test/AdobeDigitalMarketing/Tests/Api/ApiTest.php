<?php

abstract class AdobeDigitalMarketing_Tests_ApiTest extends PHPUnit_Framework_TestCase
{
    protected $username    = 'YOUR USERNAME';
    protected $secret      = 'YOUR SECRET';
    protected $reportSuite = 'YOUR REPORT SUITE'
    
    abstract protected function getApiClass();

    protected function getApiMock()
    {
        return $this->getMockBuilder($this->getApiClass())
            ->setMethods(array('get', 'post'))
            ->disableOriginalConstructor()
            ->getMock();
    }
    
    protected function getApi()
    {
        $api = new AdobeDigitalMarketing_Client();
        $api->authenticate($this->username, $this->secret);
        
        return $api;
    }
}
