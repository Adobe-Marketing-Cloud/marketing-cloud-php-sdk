<?php

namespace AdobeMarketingCloud\Api;

use AdobeMarketingCloud\Auth\Pass;

/**
 * Api calls for getting data about a company
 *
 * @link      https://marketing.adobe.com/developer/documentation/analytics-administration-1-4/r-getreportsuites-1
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class Company extends SuiteApi
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
        $this->client->setAuthService(new Pass());

        // make the call
        $response = $this->post('Company.GetEndpoint', array('company' => $companyName));

        // set the auth back to what it was before
        $this->client->getHttpClient()->setAuthService($auth);

        return $response;
    }
}
