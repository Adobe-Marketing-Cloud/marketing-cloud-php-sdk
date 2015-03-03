<?php

namespace AdobeMarketingCloud;

/**
 * Simple PHP AdobeMarketingCloud API
 *
 * @tutorial  http://github.com/Adobe-Digital-Marketing/adobe-digital-marketing-php-sdk/blob/master/README.md
 * @version   2.6
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 *
 * Website: http://github.com/Adobe-Digital-Marketing/adobe-digital-marketing-php-sdk
 * Tickets: http://github.com/Adobe-Digital-Marketing/adobe-digital-marketing-php-sdk/issues
 */
class Client
{
    /**
     * The request instance used to communicate with AdobeMarketingCloud
     * @var HttpClient
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
     * Instanciate a new AdobeMarketingCloud client
     *
     * @param  HttpClientInterface $httpClient custom http client
     */
    public function __construct(HttpClientInterface $httpClient = null)
    {
        if (null === $httpClient) {
            $this->httpClient = new HttpClient\Curl();
        } else {
            $this->httpClient = $httpClient;
        }
    }

    /**
     * Authenticate a user for all next requests
     *
     * @param  string         $username      AdobeMarketingCloud Web Services username
     * @param  string         $secret        AdobeMarketingCloud Web Services secret
     * @return AdobeMarketingCloud               fluent interface
     */
    public function authenticate()
    {
        $auth = $this->getHttpClient()->getAuthService();

        $args = func_get_args(); // php 5.2 requires this be on a separate line

        call_user_func_array(array($auth, 'authenticate'), $args);

        return $this;
    }

    /**
     * De-authenticate a user for all next requests
     *
     * @return Api               fluent interface
     */
    public function deAuthenticate()
    {
        return $this->authenticate(null, null);
    }

    /**
     * Set the Auth service on your Http Client
     *
     * @param  AuthInterface     $auth      Auth Interface
     * @return Api               fluent interface
     */
    public function setAuthService(AuthInterface $auth)
    {
        $this->getHttpClient()->setAuthService($auth);

        return $this;
    }

    /**
     * Call any route, GET method
     * Ex: $api->get('repos/show/my-username/my-repo')
     *
     * @param   string  $route            the AdobeMarketingCloud route
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
     * @param   string  $route            the AdobeMarketingCloud route
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
     * @param   string  $route            the AdobeMarketingCloud route
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
     * @param   string  $route            the AdobeMarketingCloud route
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
     * @return  HttpClientInterface   an httpClient instance
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Inject another request
     *
     * @param   HttpClientInterface   a httpClient instance
     * @return  Api          fluent interface
     */
    public function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    /**
     * Get the suite API
     * generic API to call get/post directly
     *
     * @return  Api\SuiteApi  the report API
     */
    public function getSuiteApi($options = array())
    {
        if(!isset($this->apis['suite']))
        {
            $this->apis['suite'] = new Api\SuiteApi($this, $options);
        }

        return $this->apis['suite'];
    }

    /**
     * Get the report API
     *
     * @param array $options
     * @return Api\Report
     */
    public function getReportApi($options = array())
    {
        if(!isset($this->apis['report']))
        {
            $this->apis['report'] = new Api\Report($this, $options);
        }

        return $this->apis['report'];
    }

    /**
     * Get the permissions API
     *
     * @return  Api\Permissions  the permissions API
     */
    public function getPermissionsApi($options = array())
    {
        if(!isset($this->apis['permissions']))
        {
            $this->apis['permissions'] = new Api\Permissions($this, $options);
        }

        return $this->apis['permissions'];
    }

    /**
     * Get the company API
     *
     * @return  Api\Company  the company API
     */
    public function getCompanyApi($options = array())
    {
        if(!isset($this->apis['company']))
        {
            $this->apis['company'] = new Api\Company($this, $options);
        }

        return $this->apis['company'];
    }

    /**
     * Get the report suite API
     *
     * @return  Api\ReportSuite  the report suite API
     */
    public function getReportSuiteApi($options = array())
    {
        if(!isset($this->apis['report_suite']))
        {
            $this->apis['report_suite'] = new Api\ReportSuite($this, $options);
        }

        return $this->apis['report_suite'];
    }


    /**
     * Get the OAuth API
     *
     * @return  Api\OAuth  the company API
     */
    public function getOAuthApi($options = array())
    {
        if(!isset($this->apis['oauth']))
        {
            $this->apis['oauth'] = new Api\OAuth($this, $options);
        }

        return $this->apis['oauth'];
    }

    /**
     * Inject another API instance
     *
     * @param   string                $name the API name
     * @param   AdobeMarketingCloud   $api  the API instance
     * @return  Client       fluent interface
     */
    public function setApi($name, ApiInterface $instance)
    {
        $this->apis[$name] = $instance;

        return $this;
    }

    /**
     * returns the most recent response for debugging purposes (see AdobeMarketingCloud\HttpClient::getLastResponse)
     */
    public function getLastResponse()
    {
        return $this->getHttpClient()->getLastResponse();
    }

    /**
     * Get any API
     *
     * @param   string                    $name the API name
     * @return  AdobeMarketingCloud\Api\Abstract     the API instance
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
