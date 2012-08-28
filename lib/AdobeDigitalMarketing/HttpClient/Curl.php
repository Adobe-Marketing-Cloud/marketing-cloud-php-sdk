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
        $headers = array();

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
                $curlOptions += array(
                    CURLOPT_POSTFIELDS  => json_encode($parameters)
                );
                $headers[] = 'Content-Type: application/json';
            }
        } else {
            $headers[] = 'Content-Length: 0';
        }

        $this->debug('send '.$httpMethod.' request: '.$url);

        $curlOptions += array(
            CURLOPT_URL             => $url,
            CURLOPT_PORT            => $options['http_port'],
            CURLOPT_USERAGENT       => $options['user_agent'],
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_TIMEOUT         => $this->options['timeout'],
            CURLOPT_HTTPHEADER      => $headers,
			CURLOPT_SSL_VERIFYPEER  => !isset($this->options['verifyssl']) || $this->options['verifyssl'],
        );

        $response = $this->doCurlCall($curlOptions);

        return $response;
    }
    
    protected function generateWsseHeader($username, $secret)
    {
        $nonce = md5(rand());
        $created = gmdate('Y-m-d H:i:s T');

        $digest = base64_encode(sha1($nonce.$created.$secret,true));
        $b64nonce = base64_encode($nonce);

        return sprintf('X-WSSE: UsernameToken Username="%s", PasswordDigest="%s", Nonce="%s", Created="%s"',
          $username,
          $digest,
          $b64nonce,
          $created
        );
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
