<?php

abstract class AdobeDigitalMarketing_Tests_ApiTest extends PHPUnit_Framework_TestCase
{
    protected $username;
    protected $secret;
    protected $reportSuite;

    public function initialize($options)
    {
        $this->username    = $options['username'];
        $this->secret      = $options['secret'];
        $this->reportSuite = $options['reportSuite'];
    }
    
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
        $this->initializeFromGlobals();

        $api = new AdobeDigitalMarketing_Client();
        $api->authenticate($this->username, $this->secret);
        
        return $api;
    }

    protected function initializeFromGlobals()
    {
        if (!isset($_SERVER['AdobeDigitalMarketing_Test_Username'])
            || !isset($_SERVER['AdobeDigitalMarketing_Test_Secret'])
            || !isset($_SERVER['AdobeDigitalMarketing_Test_ReportSuite'])) {
            throw new AdobeDigitalMarketing_HttpClient_AuthenticationException("You must define a username/secret/reportsuite for testing in an environment variable (AdobeDigitalMarketing_Test_Username, AdobeDigitalMarketing_Test_Secret, AdobeDigitalMarketing_Test_ReportSuite)");
        }
        
        $options = array(
            'username'    => $_SERVER['AdobeDigitalMarketing_Test_Username'],
            'secret'      => $_SERVER['AdobeDigitalMarketing_Test_Secret'],
            'reportSuite' => $_SERVER['AdobeDigitalMarketing_Test_ReportSuite'],
        );
        
        if (isset($_SERVER['AdobeDigitalMarketing_Test_Endpoint'])) {
            $options['endpoint'] = $_SERVER['AdobeDigitalMarketing_Test_Endpoint'];
        }
        
        $this->initialize($options);
        
        return $this;
    }
}
