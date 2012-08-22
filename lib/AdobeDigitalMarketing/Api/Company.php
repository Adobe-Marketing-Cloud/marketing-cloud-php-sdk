<?php

/**
 * Api calls for getting data about a company
 *
 * @link      https://developer.omniture.com/en_US/documentation/omniture-administration/c-api-admin-methods-permissions
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class AdobeDigitalMarketing_Api_Company extends AdobeDigitalMarketing_Api
{
    /**
     * Internal method to authenticate a set of credentials
     * Credentials can be found in the SiteCatalyst Admin Console
     *   Admin > Admin Console > Company > Web Services
     *
     * @return  array - list of report suites
     */
    public function getReportSuites()
    {
        $response = $this->post('Company.GetReportSuites');

        return $this->returnResponse($response);
    }
}
