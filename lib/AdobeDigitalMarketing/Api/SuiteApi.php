<?php

/**
 * Common base class for all APIs calling the 1.2 and 1.3-style methods of the API
 *
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class AdobeDigitalMarketing_Api_SuiteApi extends AdobeDigitalMarketing_Api
{
    /**
     * Call any path, GET method
     * Ex: $api->get('artist/biographies', array('name' => 'More Hazards More Heroes'))
     *
     * @param   string  $path             the AdobeDigitalMarketing path
     * @param   array   $parameters       GET parameters
     * @param   array   $requestOptions   reconfigure the request
     * @return  array                     data returned
     */
    public function get($method, array $parameters = array(), $requestOptions = array())
    {
        $parameters['method'] = $method;
        return parent::get($this->getSuitePath(), $parameters, $requestOptions);
    }

    /**
     * Call any path, POST method
     * Ex: $api->post('catalog/create', array('type' => 'artist', 'name' => 'My Catalog'))
     *
     * @param   string  $path             the AdobeDigitalMarketing path
     * @param   array   $parameters       POST parameters
     * @param   array   $requestOptions   reconfigure the request
     * @return  array                     data returned
     */
    public function post($method, array $parameters = array(), $requestOptions = array())
    {
        return parent::post($this->getSuitePath($method), $parameters, $requestOptions);
    }

    private function getSuitePath($method = null)
    {
        return sprintf('admin/%s/rest/%s', $this->client->getHttpClient()->getOption('api_version'), is_null($method) ? '' : '?method='.$method);
    }
}
