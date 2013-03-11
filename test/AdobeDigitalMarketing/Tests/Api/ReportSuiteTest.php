<?php

class AdobeDigitalMarketing_Tests_Api_ReportSuiteTest extends AdobeDigitalMarketing_Tests_ApiTest
{
    public function testGetEvars()
    {
        $api = $this->getClient()->getReportSuiteApi();

        $response = $api->getEvars(array($this->reportSuite));

        $this->assertTrue(count($response) > 0);
        $this->assertTrue(is_array($response[0]));
        $this->assertArrayHasKey("evars", $response[0]);
        $this->assertTrue(count($response[0]['evars']) > 0);
    }

    public function testGetEvents()
    {
        $api = $this->getClient()->getReportSuiteApi();

        $response = $api->getEvents(array($this->reportSuite));

        $this->assertTrue(count($response) > 0);
        $this->assertTrue(is_array($response[0]));
        $this->assertArrayHasKey("events", $response[0]);
        $this->assertTrue(count($response[0]['events']) > 0);
    }

    public function testGetProps()
    {
        $api = $this->getClient()->getReportSuiteApi();

        $response = $api->getProps(array($this->reportSuite));

        $this->assertTrue(count($response) > 0);
        $this->assertTrue(is_array($response[0]));
        $this->assertArrayHasKey("traffic_vars", $response[0]);
        $this->assertTrue(count($response[0]['traffic_vars']) > 0);
    }

    public function getApiClass()
    {
        return 'AdobeDigitalMarketing_Api_Company';
    }
}