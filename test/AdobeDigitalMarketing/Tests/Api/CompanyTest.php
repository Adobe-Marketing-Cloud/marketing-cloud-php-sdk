<?php

class AdobeDigitalMarketing_Tests_Api_CompanyTest extends AdobeDigitalMarketing_Tests_ApiTest
{
    public function testQueueTrended()
    {
        $client = new AdobeDigitalMarketing_Client();

        $response = $client->getCompanyApi()->getEndpoint('Adobe');

        if (!is_string($response)) {
            print_r($client->getLastResponse()); // for debugging
        }
        $this->assertTrue(is_string($response));
    }

    public function getApiClass()
    {
        return 'AdobeDigitalMarketing_Api_Company';
    }
}