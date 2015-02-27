<?php

namespace AdobeMarketingCloud\Tests;

use AdobeMarketingCloud\Client;
use AdobeMarketingCloud\HttpClient\Curl;
use AdobeMarketingCloud\HttpClient\SoapClient;
use AdobeMarketingCloud\Auth\Exception;
use AdobeMarketingCloud\Auth\OAuth2;

abstract class BaseTestCase extends \PHPUnit_Framework_TestCase
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
           'client_id'      => isset($_SERVER['AMC_CLIENTID']) ? $_SERVER['AMC_CLIENTID'] : null,
           'client_secret'  => isset($_SERVER['AMC_CLIENTSECRET']) ? $_SERVER['AMC_CLIENTSECRET'] : null,
           'username'       => isset($_SERVER['AMC_USERNAME']) ? $_SERVER['AMC_USERNAME'] : null,
           'password'       => isset($_SERVER['AMC_PASSWORD']) ? $_SERVER['AMC_PASSWORD'] : null,
           'secret'         => isset($_SERVER['AMC_SECRET']) ? $_SERVER['AMC_SECRET'] : null,
           'reportSuite'    => isset($_SERVER['AMC_REPORTSUITE']) ? $_SERVER['AMC_REPORTSUITE'] : null,
           'endpoint'       => isset($_SERVER['AMC_ENDPOINT']) ? $_SERVER['AMC_ENDPOINT'] : null,
           'debug'          => isset($_SERVER['AMC_DEBUG']) ? $_SERVER['AMC_DEBUG'] : true,
           'proxy'          => isset($_SERVER['AMC_PROXY']) ? $_SERVER['AMC_PROXY'] : null,
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

                $client = new Client(new Curl($options));
                if ($authenticate) {
                    $client->authenticate($options['username'], $options['secret']);
                }
                break;
            case 'soap':
                $this->initializeWsseFromGlobals();
                $options = $this->getOptionsFromGlobals();

                $client = new Client(new SoapClient($options));
                if ($authenticate) {
                    $client->authenticate($options['username'], $options['secret']);
                }
                break;
            case 'oauth':
                $this->initializeOAuthFromGlobals();
                $options = $this->getOptionsFromGlobals();

                $client = new Client(new Curl($options));
                if ($authenticate) {
                    $client->setAuthService(new OAuth2())
                        ->authenticate($options['client_id'], $options['client_secret']);
                }
                break;
        }

        return $client;
    }

    protected function initializeWsseFromGlobals()
    {
        if (!isset($_SERVER['AMC_USERNAME'])
            || !isset($_SERVER['AMC_SECRET'])
            || !isset($_SERVER['AMC_REPORTSUITE'])) {
            throw new Exception("You must define a username/secret/reportsuite for testing in an environment variable (AMC_USERNAME, AMC_SECRET, AMC_REPORTSUITE)");
        }
    }

    protected function initializeOAuthFromGlobals()
    {
        if (!isset($_SERVER['AMC_USERNAME'])
            || !isset($_SERVER['AMC_PASSWORD'])
            || !isset($_SERVER['AMC_CLIENTID'])
            || !isset($_SERVER['AMC_CLIENTSECRET'])) {
            throw new Exception("You must define a client_id/client_secret/username/secret/reportsuite for testing in an environment variable (AMC_CLIENTID, AMC_CLIENTSECRET, AMC_USERNAME, AMC_PASSWORD)");
        }
    }
}
