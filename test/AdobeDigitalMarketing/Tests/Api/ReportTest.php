<?php

class AdobeDigitalMarketing_Tests_Api_ReportTest extends AdobeDigitalMarketing_Tests_ApiTest
{
    public function testQueueTrended()
    {
        $api = $this->getApi()->getReportApi();

        $response = $api->queueTrended(array(
           'reportSuiteID' => 'YOUR REPORT SUITE',
           'date'          => date('Y-m-d', strtotime('-1 days')),
           'metrics'       => array('pageviews'),
        ));
        
        exit(var_dump($response));
    }

    protected function getApiClass()
    {
        return 'AdobeDigitalMarketing_Api_Report';
    }
}
