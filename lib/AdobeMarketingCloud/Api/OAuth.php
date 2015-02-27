<?php

namespace AdobeMarketingCloud\Api;

use AdobeMarketingCloud\Api;

/**
 * Api calls for getting data about a company
 *
 * @link      https://developer.omniture.com/en_US/documentation/omniture-administration/c-api-admin-methods-permissions
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class OAuth extends Api
{
    public function getTokenFromUserCredentials($username, $password, $scope = null)
    {
        $response = $this->post('token', array(
                'grant_type'    => 'password',
                'username'      => $username,
                'password'      => $password,
                'scope'         => $scope,
        ));

        if (!isset($response['access_token'])) {
            return null;
        }

        return $this->returnResponse($response);
    }
}
