<?php

/**
 * Api calls for getting data about a company
 *
 * @link      https://developer.omniture.com/en_US/documentation/omniture-administration/c-api-admin-methods-permissions
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class AdobeDigitalMarketing_Api_Company extends AdobeDigitalMarketing_Api_SuiteApi
{
    /**
     * Retrieve report suites for your company
     *
     * @return  array - list of report suites
     */
    public function getReportSuites()
    {
        $response = $this->post('Company.GetReportSuites');

        return $this->returnResponse($response);
    }
}
