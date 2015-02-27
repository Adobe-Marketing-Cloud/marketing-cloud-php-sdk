<?php

namespace AdobeMarketingCloud\Tests\HttpClient;

use AdobeMarketingCloud\Tests\BaseTestCase;
use AdobeMarketingCloud\Client;
use AdobeMarketingCloud\HttpClient\Curl;

class CurlTest extends BaseTestCase
{
    public function testGetResponseHeaders()
    {
        $options = $this->getOptionsFromGlobals();
        $options['curlopts'] = array(CURLOPT_HEADER => false);

        $client = new Client(new Curl($options));

        $response = $client->getCompanyApi()->getEndpoint('Shaffer Corp SBX');
        $fullResponse = $client->getLastResponse();

        $this->assertFalse(isset($fullResponse['headers']['response_headers']));

        // enable the response headers in curl
        $client->getHttpClient()->setOption('curlopts', array(CURLOPT_HEADER => true));

        $response = $client->getCompanyApi()->getEndpoint('Shaffer Corp SBX');
        $fullResponse = $client->getLastResponse();

        $this->assertTrue(isset($fullResponse['headers']['response_headers']));
        $this->assertEquals('application/json', $fullResponse['headers']['response_headers']['content-type']);

    }
}
