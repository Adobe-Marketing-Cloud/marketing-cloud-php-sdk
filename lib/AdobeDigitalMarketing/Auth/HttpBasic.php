<?php

/**
 * Performs requests on AdobeDigitalMarketing API. API documentation should be self-explanatory.
 *
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class AdobeDigitalMarketing_Auth_HttpBasic implements AdobeDigitalMarketing_AuthInterface
{
    private $username;
    private $password;
    
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }
    
    public function setAuthHeadersAndParameters(array $headers, array $parameters, array $options = array())
    {
        $headers[] = sprintf('Authorization: Basic %s', base64_encode($this->username . ':' . $this->password));
        return array($headers, $parameters);
    }
}
