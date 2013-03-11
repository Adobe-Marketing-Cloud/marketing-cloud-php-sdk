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
     * Retrieve report suites for your company
     *
     * @return  array - list of report suites
     */
    public function getEvars(array $rsidList)
    {
        $response = $this->post('ReportSuite.GetEVars', array('rsid_list' => $rsidList));

        return $this->returnResponse($response);
    }

    /**
     * Retrieve props for your report suites
     *
     * @return  array - list of report suites
     */
    public function getProps(array $rsidList)
    {
        $response = $this->post('ReportSuite.GetTrafficVars', array('rsid_list' => $rsidList));

        return $this->returnResponse($response);
    }

    /**
     * Retrieve events for your report suites
     *
     * @return  array - list of report suites
     */
    public function getEvents(array $rsidList)
    {
        $response = $this->post('ReportSuite.GetSuccessEvents', array('rsid_list' => $rsidList));

        return $this->returnResponse($response);
    }
}
