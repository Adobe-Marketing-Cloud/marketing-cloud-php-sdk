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
        'url'         => ':protocol://api.omniture.com/admin/:api_version/rest/?method=:method',
        'user_agent'  => 'php-adobedigitalmarketing-api (http://github.com/Adobe-Digital-Marketing)',
        'http_port'   => 443,
        'timeout'     => 20,
        'username'    => null,
        'secret'      => null,
        'format'      => 'json',
        'limit'       => false,
        'debug'       => false
    );

    /**
     * Instanciate a new request
     *
     * @param  array   $options  Request options
     */
    public function __construct(array $options = array())
    {
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
    public function get($method, array $parameters = array(), array $options = array())
    {
        return $this->request($method, $parameters, 'GET', $options);
    }

    /**
     * Send a POST request
     * @see send
     */
    public function post($method, array $parameters = array(), array $options = array())
    {
        return $this->request($method, $parameters, 'POST', $options);
    }

    /**
     * Send a request to the server, receive a response,
     * decode the response and returns an associative array
     *
     * @param  string   $method         Requested API method
     * @param  array    $parameters     Parameters
     * @param  string   $httpMethod     HTTP method to use
     * @param  array    $options        Request options
     *
     * @return array                    Data
     */
    public function request($method, array $parameters = array(), $httpMethod = 'GET', array $options = array())
    {
        $options = array_merge($this->options, $options);

        // create full url
        $url = strtr($options['url'], array(
          ':api_version' => $this->options['api_version'],
          ':protocol'    => $this->options['protocol'],
          ':method'      => $method
        ));

        // get encoded response
        $response = $this->doRequest($url, $parameters, $httpMethod, $options);

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
     * @return dmConfigurable The current object instance
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
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
                return json_decode($response, true);

            case 'jsonp':
                throw new LogicException("format 'jsonp' not yet supported by this library");

            case 'xml':
                throw new LogicException("format 'xml' not yet supported by this library");

            case 'xspf':
                throw new LogicException("format 'xspf' not yet supported by this library");
        }

        throw new LogicException(__CLASS__.' only supports json, json, xml, and xspf formats, '.$this->options['format'].' given.');
    }
}