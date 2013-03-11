<?php

class AdobeDigitalMarketing_Tests_Api_ReportSuiteTest extends AdobeDigitalMarketing_Tests_ApiTest
{
    public function testGetElements()
    {
        $api = $this->getClient()->getReportSuiteApi();

        $response = $api->getElements(array($this->reportSuite));

        $this->assertTrue(count($response) > 0);
        $this->assertTrue(is_array($response[0]));
        $this->assertEquals($response[0]['rsid'], $this->reportSuite);
        $this->assertArrayHasKey("available_elements", $response[0]);
        $this->assertTrue(count($response[0]['available_elements']) > 0);

        $response = $api->getElements(array($this->reportSuite), true);

        // combine all elements into a single array of IDs
        $this->assertTrue(count($response) > 0);
        $this->assertFalse(is_array($response[0]));
        $this->assertTrue(false !== array_search('evar1', $response));
    }

    public function testGetMetrics()
    {
        $api = $this->getClient()->getReportSuiteApi();

        $response = $api->getMetrics(array($this->reportSuite));

        $this->assertTrue(count($response) > 0);
        $this->assertTrue(is_array($response[0]));
        $this->assertEquals($response[0]['rsid'], $this->reportSuite);
        $this->assertArrayHasKey("available_metrics", $response[0]);
        $this->assertTrue(count($response[0]['available_metrics']) > 0);

        $response = $api->getMetrics(array($this->reportSuite), true);

        // combine all metrics into a single array of IDs
        $this->assertTrue(count($response) > 0);
        $this->assertFalse(is_array($response[0]));
        $this->assertTrue(false !== array_search('pageviews', $response));
    }

    public function getApiClass()
    {
        return 'AdobeDigitalMarketing_Api_Company';
    }
}