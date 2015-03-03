<?php

namespace AdobeMarketingCloud\Api;

/**
 * Api calls for getting data about reports.
 *
 * @link      https://marketing.adobe.com/developer/documentation/analytics-reporting-1-4/get-started
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class Report extends SuiteApi
{
    /**
     * Retrieve the elements to call from your report suite
     *
     * @param string $reportSuiteID
     * @param bool $returnAsIdArray
     *
     * @return array
     */
    public function getElements($reportSuiteID, $returnAsIdArray = false)
    {
        $response = $this->post('Report.GetElements', array('reportSuiteID' => $reportSuiteID));
        if ($returnAsIdArray) {
            $filtered = array();
            foreach ($response as $element) {
                $filtered[$element['id']] = $element['name'];
            }
            return array_unique($filtered);
        }
        return $this->returnResponse($response);
    }

    /**
     * Retrieve events for your report suites
     *
     * @param string $reportSuiteID
     * @param bool $returnAsIdArray
     *
     * @return array
     */
    public function getMetrics($reportSuiteID, $returnAsIdArray = false)
    {
        $response = $this->post('Report.GetMetrics', array('reportSuiteID' => $reportSuiteID));
        if ($returnAsIdArray) {
            $filtered = array();
            foreach ($response as $metric) {
                $filtered[$metric['id']] = $metric['name'];
            }
            return array_unique($filtered);
        }
        return $this->returnResponse($response);
    }

    /**
     * Submits a report request.
     *
     * https://marketing.adobe.com/developer/documentation/analytics-reporting-1-4/r-queue
     *
     * @param   array $reportDescription  the report description array
     * @return  array
     *   - reportID: The report ID of the queued report
     */
    public function queueReport($reportDescription)
    {
        $response = $this->post('Report.Queue', array(
            'reportDescription' => $reportDescription,
        ));

        return $this->returnResponse($response);
    }

    /**
     * Gets a report by report ID
     * https://marketing.adobe.com/developer/documentation/analytics-reporting-1-4/r-get
     *
     * @param   mixed     The report ID or report description
     * @return  tns:report      A structure containing the report data for the specified reportID
     */
    public function getReport($report)
    {
        if (is_numeric($report)) {
            // try to get the report
            $response = $this->retryWhileNotReady($report);
        } else {
            $response = $this->Report($report);
        }

        return $response;
    }

    /**
     * Cancels a previously submitted report request, and removes it from the processing queue.
     * https://marketing.adobe.com/developer/documentation/analytics-reporting-1-4/r-cancel
     *
     * @param   int     The report ID returned as part of the "queue report" request.
     * @return  int     Returns 1 if the operation is successful, or 0 if the operation failed.
     */
    public function cancelReport($reportId)
    {
        $response = $this->post('Report.Cancel', array(
            'reportID' => $reportId,
        ));

        return $this->returnResponse($response);
    }

    /**
     * Returns a list of reports in the specified company's report queue.
     * https://marketing.adobe.com/developer/documentation/analytics-reporting-1-4/r-getqueue-2
     *
     * @param   none
     * @return  tns:report_queue    A list of the company's currently queued report requests. SiteCatalyst determines the company by the authentication credentials provided with the request.
     */
    public function getQueue()
    {
        $response = $this->post('Report.GetQueue');

        return $this->returnResponse($response);
    }

    /**
     * Returns a report. Queues the report synchronously.
     * @see Report::queueAndGetReport()
     */
    public function Report($reportDescription)
    {
        $response = $this->queueReport($reportDescription);
        return $this->retryWhileNotReady($response['reportID']);
    }

    /**
     * Returns a report synchronously, calling getReport every two seconds until the report is ready
     *
     * @param $reportId
     * @return tns|bool
     */
    protected function retryWhileNotReady($reportId)
    {
        $attempts = 0;
        do {
            $report = $this->post('Report.Get', array(
                'reportID' => $reportId,
            ));
            $sleep = $this->getSleepSeconds(++$attempts, 50);
            if ($sleep !== false) {
                sleep($sleep);
            }
        } while (isset($report['error']) && $report['error'] == 'report_not_ready');

        if (isset($report['report'])) {
            $report = $report['report'];
        } else {
            throw new ReportError($report['error_description'], null);
        }

        return $report;
    }


    /**
    * Determines next sleep time for report queue checking.
    * Uses an incredibly complex backing off algorithm so that long requests don't have to check as often.
    *
    * @param    $attempts      int    The number of checks so far
    * @param    $maxAttempts    int User specified maximum number of checks
    *
    * @return    mixed to stop checking OR the number of seconds for the next sleep
    */
    protected function getSleepSeconds($attempts, $maxAttempts = null)
    {
        if ($maxAttempts && $attempts >= $maxAttempts) {
            return false;
        }

        // very complex.
        return $attempts * $attempts;
    }


    protected function returnResponse($response, $key = null)
    {
        if (isset($response['status']) && 0 === strpos($response['status'], 'error')) {
            throw new ReportError($response['statusMsg'], $response['status']);
        }

        return parent::returnResponse($response, $key);
    }
}
