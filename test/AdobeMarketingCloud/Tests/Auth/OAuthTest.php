<?php

namespace AdobeMarketingCloud\Tests\Auth;

use AdobeMarketingCloud\Tests\BaseTestCase;

use AdobeMarketingCloud\Auth\OAuth2;

class OAuthTest extends BaseTestCase
{
    protected $client_id;
    protected $client_secret;
    protected $username;
    protected $password;

    public function testGetTokenFromUserCredentials()
    {
        return; // not supported
        $client = $this->getClient('oauth', false);
        $client->setAuthService(new OAuth2())
            ->authenticate($this->client_id, $this->client_secret);

        $oauth = $client->getOAuthApi();
        $token = $oauth->getTokenFromUserCredentials($this->username, $this->password);

        $this->assertNotNull($token);
    }
}
