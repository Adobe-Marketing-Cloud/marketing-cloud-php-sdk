<?php

namespace AdobeMarketingCloud\Tests\Api;

use AdobeMarketingCloud\Tests\BaseTestCase;

use AdobeMarketingCloud\Client;
use AdobeMarketingCloud\HttpClient\Curl;

class CompanyTest extends BaseTestCase
{
    public function testGetEndpoint()
    {
        $client = new Client(new Curl(array(
            'curlopts' => array(CURLOPT_SSLVERSION => 3), // for travis-ci
        )));

        $response = $client->getCompanyApi()->getEndpoint('Adobe');

        $this->assertTrue(is_string($response));
    }
}