<?php

/**
 * Performs requests on AdobeDigitalMarketing API using SOAP
 *
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class AdobeDigitalMarketing_HttpClient_SoapClient extends AdobeDigitalMarketing_HttpClient
{
    protected $soap;
    protected $types = array(
        'base64Binary'  => 'base64Binary',
        'boolean'       => 'boolean',
        'date'          => 'date',
        'dateTime'      => 'dateTime',
        'double'        => 'double',
        'int'           => 'int',
        'string'        => 'string',
    );
    protected $calls = array();

    protected function getSoapClient($options)
    {
        if (!isset($options['wsdl'])) {
            $options['wsdl'] = strtr(':protocol://:endpoint/admin/:api_version/?wsdl', array(
              ':api_version' => $this->options['api_version'],
              ':protocol'    => $this->options['protocol'],
              ':endpoint'    => $this->options['endpoint'],
            ));
        }

        $soapOptions = array(
            'trace'    => 1,
            'features' => SOAP_USE_XSI_ARRAY_TYPE
        );

        if (isset($options['proxy'])) {
            list($host, $port) = explode(':', $options['proxy']);
            $soapOptions['proxy_host'] = $host;
            $soapOptions['proxy_port'] = $port;
        }

        if (isset($options['soapopts'])) {
            $soapOptions += $options['soapopts'];
        }

        // create the soap client
        return new SoapClient($options['wsdl'], $soapOptions);
    }

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
        $headers = isset($options['headers']) ? $options['headers'] : array();

        if ($auth = $this->getAuthService()) {
            $options['url'] = $url;
            $options['http_method'] = $httpMethod;
            list($headers, $parameters) = $auth->setAuthHeadersAndParameters($headers, $parameters, $options);
        }

        // parse out the method being requested
        $parts = parse_url($url);
        parse_str($parts['query'], $query);
        $method = $query['method'];

        $soapHeaders = $this->createSoapAuthHeader($headers);
        $soap = $this->getSoapClient($options);

        $template = $this->getMethodParameters($soap, $method);
        $parameters = $this->orderParameters($parameters, $template);
        $error = null;
        $response = null;

        try {
            $response = $soap->__soapCall($method, $parameters, null, $soapHeaders);
        } catch (SoapFault $e) {
            if ($e->faultcode == 'Server') {
                $error = $e->getMessage();
            } else {
                $response = array(
                    'error' => $e->faultcode == 'Client' ? 'Bad Request' : $e->faultcode,
                    'error_description' => $e->getMessage(),
                );
            }
        }

        return array(
            'response' => $soap->__getLastResponse(),
            'response_obj' => $response,
            'headers'  => $soap->__getLastResponseHeaders(),
            'errorMessage' => $error,
        );
    }

    protected function decodeResponse($response)
    {
        return $this->soapObjectToArray($response['response_obj']);
    }

    private function soapObjectToArray($obj)
    {
        if ($obj instanceof SoapVar) {
            return $obj->enc_value;
        }

        if ($obj instanceof stdClass) {
            $obj = (array) $obj;
        } elseif (!is_array($obj)) {
            return $obj;
        }

        // from here on, the object is an array
        foreach ($obj as $key => $value) {
            $obj[$key] = $this->soapObjectToArray($value);
        }

        return json_decode(json_encode($obj), true);
    }

    protected function createSoapAuthHeader($headers)
    {
        $soapHeaders = array();

        if (!isset($headers[0])) {
            throw new Exception('No auth headers set');
        }

        if (preg_match('/X-WSSE: UsernameToken Username="(.*)", PasswordDigest="(.*)", Nonce="(.*)", Created="(.*)"/', $headers[0], $matches)) {
            $wsseHeader = <<<EOF
<wsse:Security SOAP-ENV:mustUnderstand="1" xmlns:wsse="http://www.omniture.com">
    <wsse:UsernameToken wsse:Id="User">
         <wsse:Username>%s</wsse:Username>
         <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest">%s</wsse:Password>
         <wsse:Nonce>%s</wsse:Nonce>
         <wsse:Created>%s</wsse:Created>
    </wsse:UsernameToken>
</wsse:Security>
EOF;

            $wsseHeader = sprintf($wsseHeader, $matches[1], $matches[2], $matches[3], $matches[4]);

            $soapVar = new SoapVar($wsseHeader, XSD_ANYXML);

            $soapHeaders[] = new SoapHeader("http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd", "Security", $soapVar);
        } else {
            throw new Exception('Invalid headers provided');
        }

        return $soapHeaders;
    }

    /**
     * this is necessary because order matters with the PHP SoapClient
     * Essentially, we fill in all the parameters with null values
     */
    protected function orderParameters($parameters, $template)
    {
        foreach ($parameters as $key => $value) {
            // if a parameter appears to be a nested type
            if (is_array($value) && !empty($value) && !isset($value[0])) {
                $parameters[$key] = $this->orderParameters($parameters[$key], $template[$key]);
            }
        }

        $defaults = array();

        foreach ($template as $key => $type) {
            $defaults[$key] = null;
        }

        $parameters = array_merge(
            $defaults,
            $parameters
        );

        // ensure there are no extra parameters
        $parameters = array_intersect_key($parameters, $template);

        return $parameters;
    }

    protected function getMethodParameters(SoapClient $soap, $method)
    {
        foreach($soap->__getTypes() as $type) {
            $this->parseTypeDefinition($type);
        }

        foreach($soap->__getFunctions() as $call) {
            $this->parseCallDefinition($call);
        }

        if (isset($this->calls[$method])) {
            return $this->resolveTypes($this->calls[$method]);
        }

        return array();
    }

    protected function parseTypeDefinition($type)
    {
        //Simple Type
        if (preg_match('/^(\w+) (\w+)$/', $type, $matches)) {
            $this->types[$matches[2]] = $matches[1];

            return;
        }

        //Array
        if (preg_match('/^(\w+) (\w+)\[\]$/', $type, $matches))
        {
            $this->types[$matches[2]] = array(
                'array' => $matches[1]
            );

            return;
        }

        //Struct
        $type = str_replace("\n","", $type);
        $type = str_replace("struct ", "", $type);
        $type = str_replace(" {", "", $type);
        $type = str_replace("}", "", $type);
        $type = str_replace(";", "", $type);
        $type = explode(" ", $type);

        $fields = array();

        for($i=1; $i<count($type); $i+=2) {
            $fields[$type[$i+1]] = $type[$i];
        }

        $this->types[$type[0]] = array(
            'struct' => $fields
        );
    }

    protected function parseCallDefinition($call)
    {
        if (substr($call, 0, 4) == "list") {
            $call = "list".trim(substr($call, strpos($call, ")")));
        }

        $call = str_replace("(", " ", $call);
        $call = str_replace(")", "", $call);
        $call = str_replace(",", "", $call);
        $call = explode(" ", $call);

        $request = array();

        for($i=2; $i+1<count($call); $i+=2) {
            $request[str_replace("$", "", $call[$i+1])] = $call[$i];
        }

        $this->calls[$call[1]] = $request;
    }

    protected function resolveTypes($fields, $ignore = array())
    {
        $resolved = array();

        foreach($fields as $name => $type) {
            $resolved[$name] = $this->resolveType($type, $ignore);
        }

        return $resolved;
    }

    protected function resolveType($type, $ignore = array())
    {
        if (array_key_exists($type, $this->types)) {
            $typeDef = $this->types[$type];

            if (is_array($typeDef)) {
                if (in_array($type, $ignore)) {
                    return "($type)";
                }

                if (isset($typeDef['array'])) {
                    $typeDef = array($this->resolveType($typeDef['array'], array_merge($ignore, array($type))));
                } else if (isset($typeDef['struct'])) {
                    $typeDef = $this->resolveTypes($typeDef['struct'], array_merge($ignore, array($type)));
                } else {
                    throw new Exception(sprintf('Invalid type: %s', $type));
                }
            }

            if (!is_array($typeDef) && $typeDef[0] != '(') {
                $typeDef = "($typeDef)";
            }

            return $typeDef;
        }

        throw new Exception(sprintf('Type not found: %s', $type));
    }
}
