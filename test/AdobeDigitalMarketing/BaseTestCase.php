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
        if (!isset($_SERVER['ADM_USERNAME'])
            || !isset($_SERVER['ADM_SECRET'])
            || !isset($_SERVER['ADM_REPORTSUITE'])) {
            throw new AdobeDigitalMarketing_Auth_Exception("You must define a username/secret/reportsuite for testing in an environment variable (ADM_USERNAME, ADM_SECRET, ADM_REPORTSUITE)");
        }

        $options = array(
            'username'    => $_SERVER['ADM_USERNAME'],
            'secret'      => $_SERVER['ADM_SECRET'],
            'reportSuite' => $_SERVER['ADM_REPORTSUITE'],
        );

        if (isset($_SERVER['ADM_ENDPOINT'])) {
            $options['endpoint'] = $_SERVER['ADM_ENDPOINT'];
        }

        $this->initialize($options);

        return $this;
    }

    protected function initializeOAuthFromGlobals()
    {
        if (!isset($_SERVER['ADM_USERNAME'])
            || !isset($_SERVER['ADM_PASSWORD'])
            || !isset($_SERVER['ADM_CLIENTID'])
            || !isset($_SERVER['ADM_CLIENTSECRET'])) {
            throw new AdobeDigitalMarketing_Auth_Exception("You must define a client_id/client_secret/username/secret/reportsuite for testing in an environment variable (ADM_CLIENTID, ADM_CLIENTSECRET, ADM_USERNAME, ADM_PASSWORD)");
        }

        $options = array(
           'client_id' => $_SERVER['ADM_CLIENTID'],
           'client_secret' => $_SERVER['ADM_CLIENTSECRET'],
           'username' => $_SERVER['ADM_USERNAME'],
           'password' => $_SERVER['ADM_PASSWORD'],
        );

        if (isset($_SERVER['ADM_ENDPOINT'])) {
            $options['endpoint'] = $_SERVER['ADM_ENDPOINT'];
        }

        $this->initialize($options);

        return $this;
    }
}
