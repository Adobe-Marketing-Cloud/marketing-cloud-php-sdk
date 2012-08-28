<?php

/**
 * Performs requests on AdobeDigitalMarketing API. API documentation should be self-explanatory.
 *
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class AdobeDigitalMarketing_Auth_OAuth2 extends AdobeDigitalMarketing_Auth_HttpBasic
{
    private $client_id;
    private $client_secret;
    private $access_token;
    
    public function authenticate($client_id, $client_secret, $access_token = null)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->access_token = $access_token;
        parent::authenticate($client_id, $client_secret);
    }
    
    // allows the setting of an access token later in the request, as not all oauth requests require an access token
    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
    }
    
    public function setAuthHeadersAndParameters(array $headers, array $parameters, array $options = array())
    {
        if(!$this->client_id || !$this->client_secret) {
            throw new AdobeDigitalMarketing_Auth_Exception("client_id and client_secret must be set before making a request");
        }
        
        if($this->access_token) {
            $parameters['oauth_token'] = $this->access_token;
        }
        $headers[] = 'Content-Type: application/json';
        
        return parent::setAuthHeadersAndParameters($headers, $parameters, $options);
    }
}
