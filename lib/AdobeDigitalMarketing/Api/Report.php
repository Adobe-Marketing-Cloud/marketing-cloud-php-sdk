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
     * Gets a report by report ID
     * https://developer.omniture.com/en_US/documentation/sitecatalyst-reporting/r-getreport
     *
     * @param   int $reportID  the report ID
     * @return  array Report
     */
    public function getReport($reportId)
    {
        $response = $this->post('Report.GetReport', array(
            'reportID' => $reportId,
        ));

        return $this->returnResponse($response);
    }
}
