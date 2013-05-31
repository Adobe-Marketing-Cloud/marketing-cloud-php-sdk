<?php

/**
 * Api calls for getting data about a company
 *
 * @link      https://developer.omniture.com/en_US/documentation/omniture-administration/c-api-admin-methods-permissions
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class AdobeDigitalMarketing_Api_OAuth extends AdobeDigitalMarketing_Api
{
    public function getTokenFromUserCredentials($username, $password)
    {
        $response = $this->post('token', array(
                'grant_type'    => 'password',
                'username'      => $username,
                'password'      => $password
        ));

        if (!isset($response['access_token'])) {
            return null;
        }

        return $this->returnResponse($response);
    }
}
