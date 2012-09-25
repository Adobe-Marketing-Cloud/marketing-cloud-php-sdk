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

    public function getEndpoint($companyName)
    {
        // we do not need authentication for this call
        $auth = $this->client->getHttpClient()->getAuthService();
        $this->client->setAuthService(new AdobeDigitalMarketing_Auth_Pass());

        // make the call
        $response = $this->post('Company.GetEndpoint', array('company' => $companyName));

        // set the auth back to what it was before
        $this->client->getHttpClient()->setAuthService($auth);

        return $response;
    }
}
