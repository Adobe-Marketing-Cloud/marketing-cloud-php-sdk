<?php

/**
 * Performs requests on AdobeDigitalMarketing API. API documentation should be self-explanatory.
 *
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
abstract class AdobeDigitalMarketing_HttpClient implements AdobeDigitalMarketing_HttpClientInterface
{
    /**
     * The request options
     * @var array
     */
    protected $options = array(
        'protocol'    => 'https',
        'api_version' => '1.3',
        'endpoint'    => 'api.omniture.com',
        'url'         => ':protocol://:endpoint/:path',
        'user_agent'  => 'adobe-digital-marketing-php-sdk (http://github.com/Adobe-Digital-Marketing)',
        'http_port'   => 443,
        'timeout'     => 20,
        'username'    => null,
        'secret'      => null,
        'format'      => 'json',
        'limit'       => false,
        'debug'       => false,
        'proxy'       => null,
        'content-type' => 'json',
        'follow-location' => true, // automatically follow "Location" header for 301 redirects
    );

    protected $auth;
    protected $lastResponse;

    /**
     * Instanciate a new request
     *
     * @param  array   $options  Request options
     */
    public function __construct(array $options = array(), AdobeDigitalMarketing_AuthInterface $auth = null)
    {
        if (is_null($auth)) {
            $auth = new AdobeDigitalMarketing_Auth_Wsse();
        }
        $this->auth = $auth;
        $this->configure($options);
    }

    /**
     * Configure the request
     *
     * @param   array               $options  Request options
     * @return  AdobeDigitalMarketingApiRequest $this     Fluent interface
     */
    public function configure(array $options)
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * Send a request to the server, receive a response
     *
     * @param  string   $url           Request url
     * @param  array    $parameters    Parameters
     * @param  string   $httpMethod    HTTP method to use
     * @param  array    $options        Request options
     *
     * @return string   HTTP response
     */
    abstract protected function doRequest($url, array $parameters = array(), $httpMethod = 'GET', array $options = array());

    /**
     * Send a GET request
     * @see send
     */
    public function get($path, array $parameters = array(), array $options = array())
    {
        return $this->request($path, $parameters, 'GET', $options);
    }

    /**
     * Send a POST request
     * @see send
     */
    public function post($path, array $parameters = array(), array $options = array())
    {
        return $this->request($path, $parameters, 'POST', $options);
    }

    /**
     * Send a PUT request
     * @see send
     */
    public function put($method, array $parameters = array(), array $options = array())
    {
        return $this->request($method, $parameters, 'PUT', $options);
    }

    /**
     * Send a DELETE request
     * @see send
     */
    public function delete($method, array $parameters = array(), array $options = array())
    {
        return $this->request($method, $parameters, 'DELETE', $options);
    }

    /**
     * Send a request to the server, receive a response,
     * decode the response and returns an associative array
     *
     * @param  string   $path           Requested API resource path
     * @param  array    $parameters     Parameters
     * @param  string   $httpMethod     HTTP method to use
     * @param  array    $options        Request options
     *
     * @return array                    Data
     */
    public function request($path, array $parameters = array(), $httpMethod = 'GET', array $options = array())
    {
        $options = array_merge($this->options, $options);

        // create full url
        $url = strtr($options['url'], array(
          ':api_version' => $this->options['api_version'],
          ':protocol'    => $this->options['protocol'],
          ':endpoint'    => $this->options['endpoint'],
          ':path'        => $path
        ));

        // get encoded response
        $response = $this->doRequest($url, $parameters, $httpMethod, $options);
        $this->lastResponse = $response; // for debugging (see getLastResponse)

        // decode response
        $response = $this->decodeResponse($response, $options);

        return $response;
    }

    /**
     * Change an option value.
     *
     * @param string $name   The option name
     * @param mixed  $value  The value
     *
     * @return The current object instance
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * Return the options array.
     *
     * @return array The httpclient options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get an option value.
     *
     * @param  string $name The option name
     *
     * @return mixed  The option value
     */
    public function getOption($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    public function getAuthService()
    {
        return $this->auth;
    }

    public function setAuthService(AdobeDigitalMarketing_AuthInterface $auth)
    {
        $this->auth = $auth;
    }

    /**
     * returns the most recent response for debugging purposes
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Get a JSON response and transform it to a PHP array
     *
     * @return  array   the response
     */
    protected function decodeResponse($response)
    {
        switch ($this->options['format'])
        {
            case 'json':
                if ((null === $json = json_decode($response['response'], true)) && ($this->options['debug'] === true)) {
                    throw new AdobeDigitalMarketing_HttpClient_Exception("Response is not in JSON format: \n\n".print_r($response, true));
                }
                return $json;

            case 'jsonp':
                throw new LogicException("format 'jsonp' not yet supported by this library");

            case 'xml':
                return json_decode(json_encode(simplexml_load_string($response['response'])), true);

            case 'xspf':
                throw new LogicException("format 'xspf' not yet supported by this library");
        }

        throw new LogicException(__CLASS__.' only supports json, json, xml, and xspf formats, '.$this->options['format'].' given.');
    }
}
