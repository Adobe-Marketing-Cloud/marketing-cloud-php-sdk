<?php

/**
 * Performs requests on AdobeDigitalMarketing API. API documentation should be self-explanatory.
 *
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class AdobeDigitalMarketing_Auth_OAuth2 implements AdobeDigitalMarketing_AuthInterface
{
    public function __construct(array $config)
    {
        $config = array_merge(array(
            'http_client'           => null,
            'client_id'             => null,
            'client_secret'         => null,
            'access_token_endpoint' => null,
            'access_token_method'   => 'authorize',
        ), $config);
        
        if (is_null($config['client_id']) || is_null($config['client_secret'])) {
            throw new AdobeDigitalMarketing_Auth_Exception('The following parameters are required: "client_id", "client_secret"');
        }
        
        if (is_null($config['http_client'])) {
            // create client for authorizing tokens, etc
            $config['http_client'] = new AdobeDigitalMarketing_HttpClient_Curl(array(
                'url'       => ':protocol://:endpoint/:method',
                'endpoint'  => $config['access_token_endpoint'],
            ), new AdobeDigitalMarketing_Auth_HttpBasic($config['client_id'], $config['client_secret']));
        }
        
        $this->config = $config;
    }
    
    public function getAccessToken($options)
    {
        // Will support other grant types later
        return $this->getAccessTokenFromPassword($options['username'], $options['secret']);
    }
    
    private function getAccessTokenFromPassword($username, $password) 
    {
        if ($this->config['access_token_method']) {
            $response = $this->config['http_client']->get($this->config['access_token_method'], array(
                'grant_type'    => 'password',
                'client_id'     => $this->config['client_id'],
                'client_secret' => $this->config['client_secret'],
                'username'      => $username,
                'password'      => $password
            ));
            
            if (!isset($response['access_token'])) {
                throw new AdobeDigitalMarketing_Auth_Exception("unable to get access token from user credentials: ".print_r($response, true));
            }

            return $response['access_token'];
        }
        return NULL;
    }
    
    public function setAuthHeadersAndParameters(array $headers, array $parameters, array $options = array())
    {
        if(!$options['username'] || !$options['secret']) {
            throw new AdobeDigitalMarketing_Auth_Exception("username and secret must be set before making a request");
        }
        
        $token = $this->getAccessToken($options);
        $parameters['oauth_token'] = $token;
        
        $headers[] = sprintf('Authorization: Basic %s', base64_encode($this->config['client_id'] . ':' . $this->config['client_secret']));
        return array($headers, $parameters);
    }
    
    public function getSupportedGrantTypes()
    {
        // only one supported grant type at this time
        return array(
            OAuth2::GRANT_TYPE_USER_CREDENTIALS,
        );
    }
}
