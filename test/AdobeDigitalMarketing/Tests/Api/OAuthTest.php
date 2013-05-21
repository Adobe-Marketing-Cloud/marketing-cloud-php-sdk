<?php

class AdobeDigitalMarketing_Tests_Auth_OAuthTest extends AdobeDigitalMarketing_BaseTestCase
{
    protected $client_id;
    protected $client_secret;
    protected $username;
    protected $password;

    public function testGetTokenFromUserCredentials()
    {
        return; // not supported
        $client = $this->getClient('oauth', false);
        $client->setAuthService(new AdobeDigitalMarketing_Auth_OAuth2())
            ->authenticate($this->client_id, $this->client_secret);

        $oauth = $client->getOAuthApi();
        $token = $oauth->getTokenFromUserCredentials($this->username, $this->password);

        $this->assertNotNull($token);
    }
}
