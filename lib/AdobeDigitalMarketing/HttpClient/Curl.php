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
        if(!$options['username'] || !$options['secret'])
        {
            throw new AdobeDigitalMarketing_HttpClient_AuthenticationException("username and secret must be set before making a request");
        }

        $curlOptions = array();

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
                    CURLOPT_POST        => true,
                    CURLOPT_POSTFIELDS  => json_encode($parameters)
                );
            }
        }

        $this->debug('send '.$httpMethod.' request: '.$url);

        $curlOptions += array(
            CURLOPT_URL             => $url,
            CURLOPT_PORT            => $options['http_port'],
            CURLOPT_USERAGENT       => $options['user_agent'],
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_TIMEOUT         => $this->options['timeout'],
            CURLOPT_HTTPHEADER      => array($this->generateWsseHeader($options['username'], $options['secret'])),
        );

        $response = $this->doCurlCall($curlOptions);

        return $response['response'];
    }
    
    protected function generateWsseHeader($username, $secret)
    {
        $nonce = md5(rand(), true);
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
