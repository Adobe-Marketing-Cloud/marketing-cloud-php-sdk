<?php

class AdobeDigitalMarketing_Tests_Api_CompanyTest extends AdobeDigitalMarketing_BaseTestCase
{
    public function testGetEndpoint()
    {
        $client = new AdobeDigitalMarketing_Client(new AdobeDigitalMarketing_HttpClient_Curl(array(
            'curlopts' => array(CURLOPT_SSLVERSION => 3), // for travis-ci
        )));

        $response = $client->getCompanyApi()->getEndpoint('Adobe');

        $this->assertTrue(is_string($response));
    }
}