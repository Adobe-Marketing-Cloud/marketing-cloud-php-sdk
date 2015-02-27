<?php

namespace AdobeMarketingCloud\Auth;

use AdobeMarketingCloud\AuthInterface;

/**
 * Performs requests on AdobeMarketingCloud API. API documentation should be self-explanatory.
 *
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class HttpBasic implements AuthInterface
{
    private $username;
    private $password;

    public function authenticate($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function setAuthHeadersAndParameters(array $headers, array $parameters, array $options = array())
    {
        if(!$this->username || !$this->password) {
            throw new Exception("username and password must be set before making a request");
        }

        $headers[] = sprintf('Authorization: Basic %s', base64_encode($this->username . ':' . $this->password));
        return array($headers, $parameters);
    }
}
