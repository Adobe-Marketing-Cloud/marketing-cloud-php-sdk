<?php

/**
 * Performs requests on AdobeDigitalMarketing API. API documentation should be self-explanatory.
 *
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class AdobeDigitalMarketing_Auth_OAuth2 implements AdobeDigitalMarketing_AuthInterface
{
    private $access_token;

    public function authenticate($access_token)
    {
        $this->access_token = $access_token;
    }

    public function setAuthHeadersAndParameters(array $headers, array $parameters, array $options = array())
    {
        $headers[] = sprintf('Authorization: Bearer %s', $this->access_token);

        return array($headers, $parameters);
    }
}
