<?php

namespace AdobeMarketingCloud\Auth;

use AdobeMarketingCloud\AuthInterface;

/**
 * For API requests not requiring authentication
 *
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class Pass implements AuthInterface
{
    public function authenticate()
    {
        throw new \LogicException('No need to call "authenticate" for "Pass" authentication');
    }

    public function setAuthHeadersAndParameters(array $headers, array $parameters, array $options = array())
    {
        return array($headers, $parameters);
    }
}
