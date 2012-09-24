<?php

abstract class AdobeDigitalMarketing_Tests_ApiTest extends PHPUnit_Framework_TestCase
{
    protected $client_id;
    protected $client_secret;
    protected $username;
    protected $password;
    protected $secret;
    protected $reportSuite;
    protected $options;

    public function initialize($options)
    {
        $options = array_merge(array(
            'client_id'     => null,
            'client_secret' => null,
            'username'      => null,
            'password'      => null,
            'secret'        => null,
            'reportSuite'   => null,
            'debug'         => true,
        ), $options);

        $this->client_id     = $options['client_id'];
        $this->client_secret = $options['client_secret'];
        $this->username      = $options['username'];
        $this->password      = $options['password'];
        $this->secret        = $options['secret'];
        $this->reportSuite   = $options['reportSuite'];
        $this->options       = $options;
    }

    abstract protected function getApiClass();

    protected function getApiMock()
    {
        return $this->getMockBuilder($this->getApiClass())
            ->setMethods(array('get', 'post'))
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getClient($auth = 'wsse', $authenticate = true)
    {
        switch ($auth) {
            case 'wsse':
                $this->initializeWsseFromGlobals();

                $client = new AdobeDigitalMarketing_Client(new AdobeDigitalMarketing_HttpClient_Curl($this->options));
                if ($authenticate) {
                    $client->authenticate($this->username, $this->secret);
                }
                break;
            case 'oauth':
                $this->initializeOAuthFromGlobals();

                $client = new AdobeDigitalMarketing_Client(new AdobeDigitalMarketing_HttpClient_Curl(array(
                    'endpoint' => $this->options['endpoint'],
                )));
                if ($authenticate) {
                    $client->setAuthService(new AdobeDigitalMarketing_Auth_OAuth2())
                        ->authenticate($this->client_id, $this->client_secret);
                }
                break;
        }

        return $client;
    }

    protected function initializeWsseFromGlobals()
    {
        if (!isset($_SERVER['AdobeDigitalMarketing_Test_Username'])
            || !isset($_SERVER['AdobeDigitalMarketing_Test_Secret'])
            || !isset($_SERVER['AdobeDigitalMarketing_Test_ReportSuite'])) {
            throw new AdobeDigitalMarketing_Auth_Exception("You must define a username/secret/reportsuite for testing in an environment variable (AdobeDigitalMarketing_Test_Username, AdobeDigitalMarketing_Test_Secret, AdobeDigitalMarketing_Test_ReportSuite)");
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

    protected function initializeOAuthFromGlobals()
    {
        if (!isset($_SERVER['AdobeDigitalMarketing_Test_Username'])
            || !isset($_SERVER['AdobeDigitalMarketing_Test_Password'])
            || !isset($_SERVER['AdobeDigitalMarketing_Test_ClientId'])
            || !isset($_SERVER['AdobeDigitalMarketing_Test_ClientSecret'])) {
            throw new AdobeDigitalMarketing_Auth_Exception("You must define a client_id/client_secret/username/secret/reportsuite for testing in an environment variable (AdobeDigitalMarketing_Test_ClientId, AdobeDigitalMarketing_Test_ClientSecret, AdobeDigitalMarketing_Test_Username, AdobeDigitalMarketing_Test_Password)");
        }

        $options = array(
           'client_id' => $_SERVER['AdobeDigitalMarketing_Test_ClientId'],
           'client_secret' => $_SERVER['AdobeDigitalMarketing_Test_ClientSecret'],
           'username' => $_SERVER['AdobeDigitalMarketing_Test_Username'],
           'password' => $_SERVER['AdobeDigitalMarketing_Test_Password'],
        );

        if (isset($_SERVER['AdobeDigitalMarketing_Test_Endpoint'])) {
            $options['endpoint'] = $_SERVER['AdobeDigitalMarketing_Test_Endpoint'];
        }

        $this->initialize($options);

        return $this;
    }
}
