<?php

class AdobeDigitalMarketing_Tests_Api_ReportTest extends AdobeDigitalMarketing_Tests_ApiTest
{
    public function testQueueTrended()
    {
        $api = $this->getApi()->getReportApi();

        $response = $api->queueTrended(array(
           'reportSuiteID' => $this->reportSuite,
           'metrics'       => array('pageviews'),
        ));
        
        var_dump($response);
    }
    
    public function testGetReport()
    {
        $api = $this->getApi()->getReportApi();

        $response = $api->queueRanked(array(
           'reportSuiteID' => $this->reportSuite,
           'date'          => date('Y-m-d'),
           'metrics'       => array('pageviews'),
           'elements'      => array('eVar1'),
        ));
        
        if (isset($response['reportID'])) {
            $report = $api->getReport($response['reportID']);
            var_dump($report);
        }
    }
    
    public function testGetRankedReport()
    {
        $api = $this->getApi()->getReportApi();

        $response = $api->getRankedReport(array(
           'reportSuiteID' => $this->reportSuite,
           'date'          => date('Y-m-d'),
           'metrics'       => array('pageviews'),
           'elements'      => array('eVar1'),
        ));
        
        $this->assertTrue(isset($response['data']));
    }

    protected function getApiClass()
    {
        return 'AdobeDigitalMarketing_Api_Report';
    }
}
