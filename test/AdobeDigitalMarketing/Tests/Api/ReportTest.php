<?php

class AdobeDigitalMarketing_Tests_Api_ReportTest extends AdobeDigitalMarketing_Tests_ApiTest
{
    public function testQueueTrended()
    {
        $api = $this->getClient()->getReportApi();

        $response = $api->queueTrended(array(
           'reportSuiteID' => $this->reportSuite,
           'metrics'       => array('pageviews'),
        ));
        
        var_dump($response);
    }
    
    public function testGetReport()
    {
        $api = $this->getClient()->getReportApi();

        $response = $api->queueRanked(array(
           'reportSuiteID' => $this->reportSuite,
           'date'          => date('Y-m-d'),
           'metrics'       => array(
               array('id' => 'pageviews'),
            ),
           'elements'      => array(
               array('id' => 'evar1'),
            ),
        ));
        
        if (isset($response['reportID'])) {
            $report = $api->getReport($response['reportID']);
            var_dump($report);
        }
    }
    
    public function testGetRankedReport()
    {
        $api = $this->getClient()->getReportApi();

        $response = $api->getRankedReport(array(
           'reportSuiteID' => $this->reportSuite,
           'date'          => date('Y-m-d'),
           'metrics'       => array(
               array('id' => 'pageviews'),
            ),
           'elements'      => array(
               array('id' => 'evar1'),
            ),
        ));
        
        $this->assertTrue(isset($response['data']));
    }

    protected function getApiClass()
    {
        return 'AdobeDigitalMarketing_Api_Report';
    }
}
