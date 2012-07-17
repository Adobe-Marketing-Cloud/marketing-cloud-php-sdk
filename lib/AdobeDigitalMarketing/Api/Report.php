<?php

/**
 * Api calls for getting data about artists.
 *
 * @link      https://developer.omniture.com/en_US/documentation/sitecatalyst-reporting/c-reporting-methods
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class AdobeDigitalMarketing_Api_Report extends AdobeDigitalMarketing_Api
{
    /**
     * Submits an Overtime report request. Overtime reports display the specified metrics over a defined time period. 
     * Overtime reports can display multiple metrics in a report. The only possible element in an Overtime report is time.
     * https://developer.omniture.com/en_US/documentation/sitecatalyst-reporting/r-queuetrended
     *
     * @param   array $reportDescription  the report description array
     * @return  array 
     *   - reportID: The report ID of the queued report
     */
    public function queueOvertime($reportDescription)
    {
        $response = $this->post('Report.QueueOvertime', array(
            'reportDescription' => $reportDescription,
        ));

        return $this->returnResponse($response);
    }
    
    /**
     * Submits a Ranked report request. Ranked reports display the rankings of the report pages in relation to the metric.
     * Ranked reports can display multiple metrics in a report.
     * Ranked reports have the following characteristics:
     * 
     *  * Display the rankings of the report pages in relation to the metric.
     *  * Display multiple metrics in a report, if desired.
     *  * Support multiple elements in a report, if desired.
     *
     * https://developer.omniture.com/en_US/documentation/sitecatalyst-reporting/r-queueranked
     *
     * @param   array $reportDescription  the report description array
     * @return  array 
     *   - reportID: The report ID of the queued report
     */
    public function queueRanked($reportDescription)
    {
        $response = $this->post('Report.QueueRanked', array(
            'reportDescription' => $reportDescription,
        ));

        return $this->returnResponse($response);
    }
    
    /**
     * Submits a Trended report request. Trended reports display trends for a single metric (revenue, orders, views, etc) and element (product, category, page, etc).
     * https://developer.omniture.com/en_US/documentation/sitecatalyst-reporting/r-queuetrended
     *
     * @param   array $reportDescription  the report description array
     * @return  array 
     *   - reportID: The report ID of the queued report
     */
    public function queueTrended($reportDescription)
    {
        $response = $this->post('Report.QueueTrended', array(
            'reportDescription' => $reportDescription,
        ));

        return $this->returnResponse($response);
    }
    
    /**
     * Gets a report by report ID
     * https://developer.omniture.com/en_US/documentation/sitecatalyst-reporting/r-getreport
     *
     * @param   int     The report ID returned as part of the "queue report" request.
     * @return  tns:report      A structure containing the report data for the specified reportID
     */
    public function getReport($reportId)
    {
        $response = $this->post('Report.GetReport', array(
            'reportID' => $reportId,
        ));

        return $this->returnResponse($response);
    }
    
    /**
     * Returns the current status of the specified report without retrieving the report data.
     * https://developer.omniture.com/en_US/documentation/sitecatalyst-reporting/r-getstatus
     *
     * @param   int     The report ID returned as part of the "queue report" request.
     * @return  tns:report_status       A structure containing status information for the specified reportID
     */
    public function getStatus($reportId)
    {
        $response = $this->post('Report.GetStatus', array(
            'reportID' => $reportId,
        ));

        return $this->returnResponse($response);
    }
    
    /**
     * Cancels a previously submitted report request, and removes it from the processing queue.
     * https://developer.omniture.com/en_US/documentation/sitecatalyst-reporting/r-cancelreport
     *
     * @param   int     The report ID returned as part of the "queue report" request.
     * @return  int     Returns 1 if the operation is successful, or 0 if the operation failed.
     */
    public function cancelReport($reportId)
    {
        $response = $this->post('Report.CancelReport', array(
            'reportID' => $reportId,
        ));

        return $this->returnResponse($response);
    }
    
    /**
     * Returns a list of reports in the specified company's report queue.
     * https://developer.omniture.com/en_US/documentation/sitecatalyst-reporting/r-getreportqueue
     *
     * @param   none
     * @return  tns:report_queue    A list of the company's currently queued report requests. SiteCatalyst determines the company by the authentication credentials provided with the request.
     */
    public function getReportQueue()
    {
        $response = $this->post('Report.GetReportQueue');

        return $this->returnResponse($response);
    }
    
    /**
     * Returns an overtime report.Queues the report synchronously.  
     * @see AdobeDigitalMarketing_Api_Report::queueAndGetReport()
     */    
    public function getOvertimeReport($reportDescription)
    {
        return $this->queueAndGetReport($reportDescription, 'Overtime');
    }

    /**
     * Returns a ranked report. Queues the report synchronously.  
     * @see AdobeDigitalMarketing_Api_Report::queueAndGetReport()
     */    
    public function getRankedReport($reportDescription)
    {
        return $this->queueAndGetReport($reportDescription, 'Ranked');
    }

    /**
     * Returns a trended report. Queues the report synchronously.  
     * @see AdobeDigitalMarketing_Api_Report::queueAndGetReport()
     */ 
    public function getTrendedReport($reportDescription)
    {
        return $this->queueAndGetReport($reportDescription, 'Trended');
    }
    
    /**
     * Returns a report synchronously, calling getReport every two seconds until the report is ready
     */
    protected function queueAndGetReport($reportDescription, $type)
    {
        $method = sprintf('queue%s', ucwords($type));
        $response = $this->$method($reportDescription);
        
        $reportId = $response['reportID'];
        
        do {
            $report = $this->getReport($reportId);
            sleep(2);
        } while ($report['status'] != 'done');
        
        return $report['report'];
    }
}
