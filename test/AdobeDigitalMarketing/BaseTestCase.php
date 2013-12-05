<?php

abstract class AdobeDigitalMarketing_BaseTestCase extends PHPUnit_Framework_TestCase
{
    protected $client_id;
    protected $client_secret;
    protected $username;
    protected $password;
    protected $secret;
    protected $reportSuite;
    protected $options;

    public function getOptionsFromGlobals()
    {
        $options = array(
           'client_id'      => isset($_SERVER['ADM_CLIENTID']) ? $_SERVER['ADM_CLIENTID'] : null,
           'client_secret'  => isset($_SERVER['ADM_CLIENTSECRET']) ? $_SERVER['ADM_CLIENTSECRET'] : null,
           'username'       => isset($_SERVER['ADM_USERNAME']) ? $_SERVER['ADM_USERNAME'] : null,
           'password'       => isset($_SERVER['ADM_PASSWORD']) ? $_SERVER['ADM_PASSWORD'] : null,
           'secret'         => isset($_SERVER['ADM_SECRET']) ? $_SERVER['ADM_SECRET'] : null,
           'reportSuite'    => isset($_SERVER['ADM_REPORTSUITE']) ? $_SERVER['ADM_REPORTSUITE'] : null,
           'endpoint'       => isset($_SERVER['ADM_ENDPOINT']) ? $_SERVER['ADM_ENDPOINT'] : null,
           'debug'          => isset($_SERVER['ADM_DEBUG']) ? $_SERVER['ADM_DEBUG'] : true,
           'proxy'          => isset($_SERVER['ADM_PROXY']) ? $_SERVER['ADM_PROXY'] : null,
        );

        return $options;
    }

    public function provideApiClients()
    {
        $options = $this->getOptionsFromGlobals();

        return array(
            array($this->getClient('wsse'), $options),
            array($this->getClient('soap'), $options),
        );
    }

    protected function getClient($auth = 'wsse', $authenticate = true)
    {
        switch ($auth) {
            case 'wsse':
                $this->initializeWsseFromGlobals();
                $options = $this->getOptionsFromGlobals();

                $client = new AdobeDigitalMarketing_Client(new AdobeDigitalMarketing_HttpClient_Curl($options));
                if ($authenticate) {
                    $client->authenticate($options['username'], $options['secret']);
                }
                break;
            case 'soap':
                $this->initializeWsseFromGlobals();
                $options = $this->getOptionsFromGlobals();

                $client = new AdobeDigitalMarketing_Client(new AdobeDigitalMarketing_HttpClient_SoapClient($options));
                if ($authenticate) {
                    $client->authenticate($options['username'], $options['secret']);
                }
                break;
            case 'oauth':
                $this->initializeOAuthFromGlobals();
                $options = $this->getOptionsFromGlobals();

                $client = new AdobeDigitalMarketing_Client(new AdobeDigitalMarketing_HttpClient_Curl($options));
                if ($authenticate) {
                    $client->setAuthService(new AdobeDigitalMarketing_Auth_OAuth2())
                        ->authenticate($options['client_id'], $options['client_secret']);
                }
                break;
        }

        return $client;
    }

    protected function initializeWsseFromGlobals()
    {
        if (!isset($_SERVER['ADM_USERNAME'])
            || !isset($_SERVER['ADM_SECRET'])
            || !isset($_SERVER['ADM_REPORTSUITE'])) {
            throw new AdobeDigitalMarketing_Auth_Exception("You must define a username/secret/reportsuite for testing in an environment variable (ADM_USERNAME, ADM_SECRET, ADM_REPORTSUITE)");
        }
    }

    protected function initializeOAuthFromGlobals()
    {
        if (!isset($_SERVER['ADM_USERNAME'])
            || !isset($_SERVER['ADM_PASSWORD'])
            || !isset($_SERVER['ADM_CLIENTID'])
            || !isset($_SERVER['ADM_CLIENTSECRET'])) {
            throw new AdobeDigitalMarketing_Auth_Exception("You must define a client_id/client_secret/username/secret/reportsuite for testing in an environment variable (ADM_CLIENTID, ADM_CLIENTSECRET, ADM_USERNAME, ADM_PASSWORD)");
        }
    }
}
