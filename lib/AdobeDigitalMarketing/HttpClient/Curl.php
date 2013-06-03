<?php

/**
 * Performs requests on AdobeDigitalMarketing API. API documentation should be self-explanatory.
 *
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class AdobeDigitalMarketing_HttpClient_Curl extends AdobeDigitalMarketing_HttpClient
{
    /**
    * Send a request to the server, receive a response
    *
    * @param  string   $apiPath       Request API path
    * @param  array    $parameters    Parameters
    * @param  string   $httpMethod    HTTP method to use
    *
    * @return string   HTTP response
    */
    protected function doRequest($url, array $parameters = array(), $httpMethod = 'GET', array $options = array())
    {
        $curlOptions = array();
        $headers = isset($options['headers']) ? $options['headers'] : array();

        if ('POST' === $httpMethod) {
            $curlOptions += array(
                CURLOPT_POST  => true,
            );
        }
        elseif ('PUT' === $httpMethod) {
            $curlOptions += array(
                CURLOPT_POST  => true, // This is so cURL doesn't strip CURLOPT_POSTFIELDS
                CURLOPT_CUSTOMREQUEST => 'PUT',
            );
        }
        elseif ('DELETE' === $httpMethod) {
            $curlOptions += array(
                CURLOPT_CUSTOMREQUEST => 'DELETE',
            );
        }

        if ($auth = $this->getAuthService()) {
            $options['url'] = $url;
            $options['http_method'] = $httpMethod;
            list($headers, $parameters) = $auth->setAuthHeadersAndParameters($headers, $parameters, $options);
        }

        if (!empty($parameters))
        {
            if('GET' === $httpMethod)
            {
                $queryString = utf8_encode($this->buildQuery($parameters));
                $url .= '?' . $queryString;
            }
            else
            {
                switch ($options['content-type']) {
                    case 'form':
                        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                        $parameters = http_build_query($parameters);
                        break;
                    case 'json':
                    default:
                        $headers[] = 'Content-Type: application/json';
                        $parameters = json_encode($parameters);
                        break;
                }

                $curlOptions += array(
                    CURLOPT_POSTFIELDS  => $parameters,
                );
            }
        } else {
            $headers[] = 'Content-Length: 0';
        }

        switch ($options['format']) {
            case 'json':
                $headers[] = 'Accept: application/json';
                break;
            case 'xml':
                $headers[] = 'Accept: text/xml';
                break;
        }

        $this->debug('send '.$httpMethod.' request: '.$url);

        // format headers
        foreach ($headers as $i => $header) {
            if (is_string($i)) {
                $headers[] = "{$i}: $header";
                unset($headers[$i]);
            }
        }

        $curlOptions += array(
            CURLOPT_URL             => $url,
            CURLOPT_PORT            => $options['http_port'],
            CURLOPT_USERAGENT       => $options['user_agent'],
            CURLOPT_FOLLOWLOCATION  => $options['follow-location'],
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_TIMEOUT         => $options['timeout'],
            CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_SSL_VERIFYPEER  => !isset($options['verifyssl']) || $options['verifyssl'],
        );

        if (isset($options['curlopts']) && is_array($options['curlopts'])) {
            $curlOptions += $options['curlopts'];
        }

        if ($options['proxy']) {
            $curlOptions[CURLOPT_PROXY] = $options['proxy'];
        }

        $response = $this->doCurlCall($curlOptions);

        return $response;
    }

    /**
     * Get a JSON response and transform it to a PHP array
     *
     * @return  array   the response
     */
    protected function decodeResponse($response)
    {
        // "false" means a failed curl request
        if (false === $response['response']) {
            $this->debug(print_r($response, true));
            return false;
        }
        return parent::decodeResponse($response);
    }

    protected function doCurlCall(array $curlOptions)
    {
        $curl = curl_init();

        curl_setopt_array($curl, $curlOptions);

        $response = curl_exec($curl);
        $headers = curl_getinfo($curl);
        $errorNumber = curl_errno($curl);
        $errorMessage = curl_error($curl);

        curl_close($curl);

        return compact('response', 'headers', 'errorNumber', 'errorMessage');
    }

    protected function buildQuery($parameters)
    {
        return http_build_query($parameters, '', '&');
    }

    protected function debug($message)
    {
        if($this->options['debug'])
        {
            print $message."\n";
        }
    }
}
