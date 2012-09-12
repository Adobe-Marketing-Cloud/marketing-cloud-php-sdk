<?php

/**
 * Api calls for getting data about permissions.
 *
 * @link      https://developer.omniture.com/en_US/documentation/omniture-administration/c-api-admin-methods-permissions
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class AdobeDigitalMarketing_Api_Permissions extends AdobeDigitalMarketing_Api_SuiteApi
{
    /**
     * Internal method to authenticate a set of credentials
     * Credentials can be found in the SiteCatalyst Admin Console
     *   Admin > Admin Console > Company > Web Services
     *
     * @param   array $username  - the web services username
     * @param   array $password  - the web services shared secret
     * @return  boolean - whether or not the authentication was successful
     */
    public function authenticate($username, $password)
    {
        $response = $this->post('Permissions.Authenticate', array(
            'login'    => $username,
            'password' => $password,
        ));

		return $this->returnResponse($response);
    }
}
