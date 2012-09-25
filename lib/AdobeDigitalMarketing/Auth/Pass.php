<?php

/**
 * For API requests not requiring authentication
 *
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class AdobeDigitalMarketing_Auth_Pass implements AdobeDigitalMarketing_AuthInterface
{
    public function authenticate()
    {
        throw new LogicException('No need to call "authenticate" for "Pass" authentication');
    }

    public function setAuthHeadersAndParameters(array $headers, array $parameters, array $options = array())
    {
        return array($headers, $parameters);
    }
}
