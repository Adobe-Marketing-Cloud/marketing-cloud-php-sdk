<?php

class AdobeDigitalMarketing_Tests_Api_ReportTest extends AdobeDigitalMarketing_BaseTestCase
{
    /** @dataProvider provideApiClients */
    public function testQueueTrended($client, $options)
    {
        $api = $client->getReportApi();

        $response = $api->queueTrended(array(
           'reportSuiteID' => $options['reportSuite'],
           'metrics'       => array('pageviews'),
           'locale'        => "en_US",
        ));

        $this->assertTrue(isset($response['reportID']));
        $this->assertNotEquals($response['reportID'], 0);
    }

    /** @dataProvider provideApiClients */
    public function testGetReport($client, $options)
    {
        $api = $this->getClient()->getReportApi();

        $response = $api->queueRanked(array(
           'reportSuiteID' => $options['reportSuite'],
           'date'          => date('Y-m-d'),
           'metrics'       => array(
               array('id' => 'pageviews'),
            ),
           'elements'      => array(
               array('id' => 'page'),
            ),
        ));

        $this->assertTrue(isset($response['reportID']));
        $report = $api->getReport($response['reportID']);
        $this->assertTrue(in_array($report['status'], array('ready', 'done', 'not ready')));
    }

    /** @dataProvider provideApiClients */
    public function testGetRankedReport($client, $options)
    {
        $api = $this->getClient()->getReportApi();

        $response = $api->getRankedReport(array(
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
