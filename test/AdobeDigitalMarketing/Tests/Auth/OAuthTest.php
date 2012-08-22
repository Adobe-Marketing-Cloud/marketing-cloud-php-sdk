<?php

class AdobeDigitalMarketing_Tests_Auth_OAuthTest extends AdobeDigitalMarketing_Tests_ApiTest
{
    public function testAuthenticate()
    {
        $api = $this->getApi()->getCompanyApi();

        $response = $api->getReportSuites();

        print_r($response);
    }
    
    protected function getApiClass()
    {
        return 'AdobeDigitalMarketing_Api_Report';
    }
    
    protected function getApi()
    {
        return $this->createFromGlobals();
    }

    protected function createFromGlobals()
    {
        if (!isset($_SERVER['AdobeDigitalMarketing_Test_Username'])
            || !isset($_SERVER['AdobeDigitalMarketing_Test_Secret'])
            || !isset($_SERVER['AdobeDigitalMarketing_Test_ClientId'])
            || !isset($_SERVER['AdobeDigitalMarketing_Test_ClientSecret'])) {
            throw new AdobeDigitalMarketing_Auth_Exception("You must define a client_id/client_secret/username/secret/reportsuite for testing in an environment variable (AdobeDigitalMarketing_Test_ClientId, AdobeDigitalMarketing_Test_ClientSecret, AdobeDigitalMarketing_Test_Username, AdobeDigitalMarketing_Test_Secret)");
        }
        
        if (!isset($_SERVER['AdobeDigitalMarketing_Test_PathToOAuth2'])) {
            throw new AdobeDigitalMarketing_Auth_Exception("You must define the path to the oauth2-php vendor in an environment variable (AdobeDigitalMarketing_Test_PathToOAuth2)");
        }
        
        $options = array(
            'username'      => $_SERVER['AdobeDigitalMarketing_Test_Username'],
            'password'      => $_SERVER['AdobeDigitalMarketing_Test_Secret'],
            'client_id'     => $_SERVER['AdobeDigitalMarketing_Test_ClientId'],
            'client_secret' => $_SERVER['AdobeDigitalMarketing_Test_ClientSecret'],
        );

        if (isset($_SERVER['AdobeDigitalMarketing_Test_AccessTokenUri'])) {
            $options['access_token_uri'] = parse_url($_SERVER['AdobeDigitalMarketing_Test_AccessTokenUri'], PHP_URL_PATH);
            $options['base_uri'] = parse_url($_SERVER['AdobeDigitalMarketing_Test_AccessTokenUri'], PHP_URL_SCHEME) . '://' .  parse_url($_SERVER['AdobeDigitalMarketing_Test_AccessTokenUri'], PHP_URL_HOST);
        }

        $api = new AdobeDigitalMarketing_Client(
            new AdobeDigitalMarketing_HttpClient_Curl(array(
                    'endpoint' => $_SERVER['AdobeDigitalMarketing_Test_Endpoint']
                ),
                new AdobeDigitalMarketing_Auth_OAuth2($options)
            )
        );
        
        return $api;
    }
}
