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
     * Submits a Ranked report request. Ranked reports display the rankings of the report pages in relation to the metric
     * https://developer.omniture.com/en_US/documentation/sitecatalyst-reporting/r-queueranked
     *
     * @param   array $reportDescription  the report description array
     * @return  array 
     *   - reportID: The report ID of the queued report
     */
    public function queueTrended($reportDescription)
    {
        $response = $this->post('Report.QueueRanked', array(
            'reportDescription' => $reportDescription,
        ));

        return $this->returnResponse($response);
    }
}
