<?php

/**
 * Api calls for getting data about a company
 *
 * @link      https://developer.omniture.com/en_US/documentation/omniture-administration/c-api-admin-methods-permissions
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class AdobeDigitalMarketing_Api_ReportSuite extends AdobeDigitalMarketing_Api_SuiteApi
{
    /**
     * Retrieve the elements to call the report API from your report suite
     *
     * @return  array - list of report suites
     */
    public function getElements(array $rsidList, $returnAsIdArray = false)
    {
        $response = $this->post('ReportSuite.GetAvailableElements', array('rsid_list' => $rsidList));

        if ($returnAsIdArray) {
            $filtered = array();
            foreach ($response as $reportSuiteElements) {
                foreach ($reportSuiteElements['available_elements'] as $element) {
                    $filtered[$element['element_name']] = $element['display_name'];
                }
            }
            return array_unique($filtered);
        }
        return $this->returnResponse($response);
    }

    /**
     * Retrieve events for your report suites
     *
     * @return  array - list of report suites
     */
    public function getMetrics(array $rsidList, $returnAsIdArray = false)
    {
        $response = $this->post('ReportSuite.GetAvailableMetrics', array('rsid_list' => $rsidList));

        if ($returnAsIdArray) {
            $filtered = array();
            foreach ($response as $reportSuiteMetrics) {
                foreach ($reportSuiteMetrics['available_metrics'] as $element) {
                    $filtered[$element['metric_name']] = $element['display_name'];
                }
            }
            return array_unique($filtered);
        }
        return $this->returnResponse($response);
    }
}
