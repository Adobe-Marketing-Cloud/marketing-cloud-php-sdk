<?php

namespace AdobeMarketingCloud\Tests\Api;

use AdobeMarketingCloud\Client;

use AdobeMarketingCloud\Tests\BaseTestCase;

class ReportTest extends BaseTestCase
{
    /** @dataProvider provideApiClients */
    public function testGetElements(Client $client, $options)
    {
        $api = $client->getReportApi();

        $response = $api->getElements($options['reportSuite']);

        $this->assertTrue(count($response) > 0);

        $response = $api->getElements($options['reportSuite'], true);

        // combine all elements into a single array of IDs
        $this->assertTrue(count($response) > 0);
        $this->assertTrue(isset($response['evar1']));
    }

    /** @dataProvider provideApiClients */
    public function testGetMetrics(Client $client, $options)
    {
        $api = $client->getReportApi();

        $response = $api->getMetrics($options['reportSuite']);

        $this->assertTrue(count($response) > 0);
        $this->assertTrue(is_array($response[0]));

        $response = $api->getMetrics($options['reportSuite'], true);

        // combine all metrics into a single array of IDs
        $this->assertTrue(count($response) > 0);
        $this->assertTrue(isset($response['instances']));
    }

    /** @dataProvider provideApiClients */
    public function testQueue(Client $client, $options)
    {
        $api = $client->getReportApi();

        $response = $api->queueReport(array(
           'reportSuiteID' => $options['reportSuite'],
           'metrics'       => array(array('id' => 'pageviews')),
           'locale'        => "en_US",
        ));

        $this->assertTrue(isset($response['reportID']));
        $this->assertNotEquals($response['reportID'], 0);
    }

    /** @dataProvider provideApiClients */
    public function testGetReport(Client $client, $options)
    {
        $api = $this->getClient()->getReportApi();

        $response = $api->getReport(array(
           'reportSuiteID' => $options['reportSuite'],
           'date'          => date('Y-m-d'),
           'metrics'       => array(
               array('id' => 'pageviews'),
            ),
           'elements'      => array(
               array('id' => 'page'),
            ),
        ));

        $this->assertTrue(isset($response['data']));
    }
}
