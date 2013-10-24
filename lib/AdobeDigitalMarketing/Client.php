<?php

/**
 * Simple PHP AdobeDigitalMarketing API
 *
 * @tutorial  http://github.com/Adobe-Digital-Marketing/adobe-digital-marketing-php-sdk/blob/master/README.md
 * @version   2.6
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 *
 * Website: http://github.com/Adobe-Digital-Marketing/adobe-digital-marketing-php-sdk
 * Tickets: http://github.com/Adobe-Digital-Marketing/adobe-digital-marketing-php-sdk/issues
 */
class AdobeDigitalMarketing_Client
{
    /**
     * The request instance used to communicate with AdobeDigitalMarketing
     * @var AdobeDigitalMarketing_HttpClient
     */
    protected $httpClient  = null;

    /**
     * The list of loaded API instances
     * @var array
     */
    protected $apis     = array();

    /**
     * Use debug mode (prints debug messages)
     * @var bool
     */
    protected $debug;

    /**
     * Instanciate a new AdobeDigitalMarketing client
     *
     * @param  AdobeDigitalMarketing_HttpClient_Interface $httpClient custom http client
     */
    public function __construct(AdobeDigitalMarketing_HttpClientInterface $httpClient = null)
    {
        if (null === $httpClient) {
            $this->httpClient = new AdobeDigitalMarketing_HttpClient_Curl();
        } else {
            $this->httpClient = $httpClient;
        }
    }

    /**
     * Authenticate a user for all next requests
     *
     * @param  string         $username      AdobeDigitalMarketing Web Services username
     * @param  string         $secret        AdobeDigitalMarketing Web Services secret
     * @return AdobeDigitalMarketingApi               fluent interface
     */
    public function authenticate()
    {
        $auth = $this->getHttpClient()->getAuthService();

        $args = func_get_args(); // php 5.2 requires this be on a separate line

        call_user_func_array(array($auth, 'authenticate'), $args);

        return $this;
    }

    /**
     * Deauthenticate a user for all next requests
     *
     * @return AdobeDigitalMarketingApi               fluent interface
     */
    public function deAuthenticate()
    {
        return $this->authenticate(null, null);
    }

    /**
     * Set the Auth service on your Http Client
     *
     * @return AdobeDigitalMarketingApi               fluent interface
     */
    public function setAuthService(AdobeDigitalMarketing_AuthInterface $auth)
    {
        $this->getHttpClient()->setAuthService($auth);

        return $this;
    }

    /**
     * Call any route, GET method
     * Ex: $api->get('repos/show/my-username/my-repo')
     *
     * @param   string  $route            the AdobeDigitalMarketing route
     * @param   array   $parameters       GET parameters
     * @param   array   $requestOptions   reconfigure the request
     * @return  array                     data returned
     */
    public function get($route, array $parameters = array(), $requestOptions = array())
    {
        return $this->getHttpClient()->get($route, $parameters, $requestOptions);
    }

    /**
     * Call any route, POST method
     * Ex: $api->post('repos/show/my-username', array('email' => 'my-new-email@provider.org'))
     *
     * @param   string  $route            the AdobeDigitalMarketing route
     * @param   array   $parameters       POST parameters
     * @param   array   $requestOptions   reconfigure the request
     * @return  array                     data returned
     */
    public function post($route, array $parameters = array(), $requestOptions = array())
    {
        return $this->getHttpClient()->post($route, $parameters, $requestOptions);
    }

    /**
     * Call any route, PUT method
     * Ex: $api->put('repos/show/my-username', array('email' => 'my-new-email@provider.org'))
     *
     * @param   string  $route            the AdobeDigitalMarketing route
     * @param   array   $parameters       PUT parameters
     * @param   array   $requestOptions   reconfigure the request
     * @return  array                     data returned
     */
    public function put($route, array $parameters = array(), $requestOptions = array())
    {
        return $this->getHttpClient()->put($route, $parameters, $requestOptions);
    }

    /**
     * Call any route, DELETE method
     * Ex: $api->delete('repos/show/my-username', array('email' => 'my-new-email@provider.org'))
     *
     * @param   string  $route            the AdobeDigitalMarketing route
     * @param   array   $parameters       DELETE parameters
     * @param   array   $requestOptions   reconfigure the request
     * @return  array                     data returned
     */
    public function delete($route, array $parameters = array(), $requestOptions = array())
    {
        return $this->getHttpClient()->delete($route, $parameters, $requestOptions);
    }

    /**
     * Get the httpClient
     *
     * @return  AdobeDigitalMarketing_HttpClient_Interface   an httpClient instance
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Inject another request
     *
     * @param   AdobeDigitalMarketing_HttpClient_Interface   a httpClient instance
     * @return  AdobeDigitalMarketingApi          fluent interface
     */
    public function setHttpClient(AdobeDigitalMarketing_HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    /**
     * Get the suite API
     * generic API to call get/post directly
     *
     * @return  AdobeDigitalMarketing_Api_Report  the report API
     */
    public function getSuiteApi($options = array())
    {
        if(!isset($this->apis['suite']))
        {
            $this->apis['suite'] = new AdobeDigitalMarketing_Api_SuiteApi($this, $options);
        }

        return $this->apis['suite'];
    }

    /**
     * Get the report API
     *
     * @return  AdobeDigitalMarketing_Api_Report  the report API
     */
    public function getReportApi($options = array())
    {
        if(!isset($this->apis['report']))
        {
            $this->apis['report'] = new AdobeDigitalMarketing_Api_Report($this, $options);
        }

        return $this->apis['report'];
    }

    /**
     * Get the permissions API
     *
     * @return  AdobeDigitalMarketing_Api_Permissions  the permissions API
     */
    public function getPermissionsApi($options = array())
    {
        if(!isset($this->apis['permissions']))
        {
            $this->apis['permissions'] = new AdobeDigitalMarketing_Api_Permissions($this, $options);
        }

        return $this->apis['permissions'];
    }

    /**
     * Get the company API
     *
     * @return  AdobeDigitalMarketing_Api_Company  the company API
     */
    public function getCompanyApi($options = array())
    {
        if(!isset($this->apis['company']))
        {
            $this->apis['company'] = new AdobeDigitalMarketing_Api_Company($this, $options);
        }

        return $this->apis['company'];
    }

    /**
     * Get the report suite API
     *
     * @return  AdobeDigitalMarketing_Api_ReportSuite  the report suite API
     */
    public function getReportSuiteApi($options = array())
    {
        if(!isset($this->apis['report_suite']))
        {
            $this->apis['report_suite'] = new AdobeDigitalMarketing_Api_ReportSuite($this, $options);
        }

        return $this->apis['report_suite'];
    }


    /**
     * Get the company API
     *
     * @return  AdobeDigitalMarketing_Api_Company  the company API
     */
    public function getOAuthApi($options = array())
    {
        if(!isset($this->apis['oauth']))
        {
            $this->apis['oauth'] = new AdobeDigitalMarketing_Api_OAuth($this, $options);
        }

        return $this->apis['oauth'];
    }

    /**
     * Inject another API instance
     *
     * @param   string                $name the API name
     * @param   AdobeDigitalMarketingApiAbstract   $api  the API instance
     * @return  AdobeDigitalMarketing_Client       fluent interface
     */
    public function setApi($name, AdobeDigitalMarketing_ApiInterface $instance)
    {
        $this->apis[$name] = $instance;

        return $this;
    }

    /**
     * returns the most recent response for debugging purposes (see AdobeDigitalMarketing_HttpClient::getLastResponse)
     */
    public function getLastResponse()
    {
        return $this->getHttpClient()->getLastResponse();
    }

    /**
     * Get any API
     *
     * @param   string                    $name the API name
     * @return  AdobeDigitalMarketing_Api_Abstract     the API instance
     */
    public function getApi($name)
    {
        return $this->apis[$name];
    }

    public function setEndpoint($endpoint)
    {
        if (0 === strpos($endpoint, 'http')) {
            $parts = parse_url($endpoint);
            $endpoint = $parts['host'];
        }
        $this->getHttpClient()->setOption('endpoint', $endpoint);
    }

    public function getEndpoint()
    {
        return $this->getHttpClient()->getOption('endpoint');
    }

    public function __clone()
    {
        // Force a copy of $httpClient, otherwise
        // it will point to same object.
        $this->httpClient = clone $this->httpClient;
    }
}
