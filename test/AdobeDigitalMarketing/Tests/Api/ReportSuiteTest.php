<?php

class AdobeDigitalMarketing_Tests_Api_ReportSuiteTest extends AdobeDigitalMarketing_BaseTestCase
{
    /** @dataProvider provideApiClients */
    public function testGetElements($client, $options)
    {
        $api = $client->getReportSuiteApi();

        $response = $api->getElements(array($options['reportSuite']));

        $this->assertTrue(count($response) > 0);
        $this->assertTrue(is_array($response[0]));
        $this->assertEquals($response[0]['rsid'], $options['reportSuite']);
        $this->assertArrayHasKey("available_elements", $response[0]);
        $this->assertTrue(count($response[0]['available_elements']) > 0);

        $response = $api->getElements(array($options['reportSuite']), true);

        // combine all elements into a single array of IDs
        $this->assertTrue(count($response) > 0);
        $this->assertTrue(isset($response['evar1']));
    }

    /** @dataProvider provideApiClients */
    public function testGetMetrics($client, $options)
    {
        $api = $client->getReportSuiteApi();

        $response = $api->getMetrics(array($options['reportSuite']));

        $this->assertTrue(count($response) > 0);
        $this->assertTrue(is_array($response[0]));
        $this->assertEquals($response[0]['rsid'], $options['reportSuite']);
        $this->assertArrayHasKey("available_metrics", $response[0]);
        $this->assertTrue(count($response[0]['available_metrics']) > 0);

        $response = $api->getMetrics(array($options['reportSuite']), true);

        // combine all metrics into a single array of IDs
        $this->assertTrue(count($response) > 0);
        $this->assertTrue(isset($response['instances']));
    }
}
