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

    public function testCurlTimeout()
    {
        $options = $this->getOptionsFromGlobals();
        $options['curlopts'] = array(CURLOPT_HEADER => false);

        $options['timeout'] = 0.5;

        $client = new Client(new Curl($options));
        $client->authenticate($options['username'], $options['secret']);

        ob_start(); // timeout causes a print_r
        $client->getCompanyApi()->getReportSuites();
        ob_clean();

        $fullResponse = $client->getLastResponse();

        $this->assertEquals($fullResponse['errorNumber'], CURLE_OPERATION_TIMEOUTED);
        $this->assertStringStartsWith('Operation timed out after ', $fullResponse['errorMessage']);
    }
}
