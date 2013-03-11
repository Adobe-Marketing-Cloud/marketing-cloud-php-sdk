<?php

/**
*
*/
class AdobeDigitalMarketing_Cli
{
    const VERSION_MAJOR = 0;
    const VERSION_MINOR = 1;
    const VERSION_PATCH = 0;

    private $supported_commands = array('authorize', 'request', 'profile');
    private $parser;

    public function __construct()
    {
        $this->parser = new AdobeDigitalMarketing_OptionParser();
        $this->parser->addHead("\nCalls the Adobe Digital Marketing Suite APIs using OAuth 2.0.");
        $this->parser->addHead("\nTo get started, call: \n");
        $this->parser->addHead("\n\t$ adm authorize\n");
        $this->parser->addHead("\nto retrieve a token.  Some other options avialable are\n");
        $this->parser->addRule('h|help', "Display a help message and exit");
        $this->parser->addRule('v|version', "Display the current api version");
        $this->parser->addRule('e|endpoint::', "Specify the api endpoint");
        $this->parser->addTail("\nSee developer.omniture.com for more information\n");
    }

    public static function run($args)
    {
        $cli = new AdobeDigitalMarketing_Cli();
        $options = $cli->parseOptions($args);
        return $cli->dispatch($options);
    }

    public function parseOptions($options)
    {
        $options = $this->parser->parse($options);

        if (count($options) == 0 || $this->parser->help) {
            $this->outputAndExit($this->parser->getUsage());
        }

        if ($this->parser->version) {
            $this->outputAndExit('Version '.implode('.', array(self::VERSION_MAJOR, self::VERSION_MINOR, self::VERSION_PATCH)));
        }

        if (!in_array($command = $options[0], $this->supported_commands)) {
            // default to "request" command - do we want to do this?
            array_unshift($options, 'request');
        }

        return $options;
    }

    public function dispatch($options)
    {
        $command = array_shift($options);

        switch ($command) {
            case 'request':
                $this->request($options);
                break;
            case 'profile':
                $this->profile($options);
                break;
            case 'authorize':
                $this->authorize($options);
                break;
        }
    }

    public function request($options)
    {
        if (count($options) < 1) {
            $this->outputAndExit('Error: you must supply the method you want to call (ex: adm request Company.GetReportSuites)');
        }

        $token = null;

        // grab the default token
        $config = $this->loadConfigFile();
        $endpoint = $this->parser->endpoint ? $this->parser->endpoint : $config['default']['endpoint'];

        foreach ($config as $clientId => $clientConf) {
            if (isset($clientConf[$endpoint]['default'])) {
                $token = $clientConf[$endpoint]['default'];
                break;
            }
            if (isset($clientConf[$endpoint]['tokens'])) {
                // grab the last token to use as a final default
                $token = array_pop($clientConf[$endpoint]['tokens']);
            }
        }

        if (!$token) {
            if ($options[0] != 'Company.GetEndpoint') {
                $this->outputAndExit('Error: No tokens found for this endpoint.  Use "authorize" method to store your credentials before making a request');
            }
        }
        $curlConf = isset($clientConf[$endpoint]) ? $clientConf[$endpoint] : array();

        $auth = new AdobeDigitalMarketing_Auth_OAuth2();
        $adm = new AdobeDigitalMarketing_Client(new AdobeDigitalMarketing_HttpClient_Curl(array('endpoint' => $endpoint) + $curlConf + $config['default'], $auth));

        $adm->authenticate($token);

        $parameters = array();
        if (isset($options[1])) {
            // load parameters from file
            if (0 === strpos($options[1], 'file://')) {
                $file = substr($options[1],7);
                if (!file_exists($file)) {
                    $this->outputAndExit(sprintf('File %s not found', $file));
                }
                $contents = trim(file_get_contents($file));
                switch(pathinfo($file, PATHINFO_EXTENSION)) {
                    case 'ini':
                        $parameters = parse_ini_string($contents);
                        break;
                    case 'xml':
                        $parameters = simplexml_load_string($contents);
                        break;
                    case 'json':
                        $parameters = json_decode($contents, 1);
                        break;
                    default:
                        parse_str($contents, $parameters);
                        break;
                }
            } else {
                // use querystring format
                parse_str($options[1], $parameters);
            }
        }
        $response = $adm->getSuiteApi()->post($options[0], $parameters);

        if (is_string($response)) {
            echo "$response\n";
        } elseif (is_array($response)) {
            echo $this->formatJson(json_encode($response));
        } else {
            print_r($adm->getLastResponse());
        }
    }

    private function getEndpoint()
    {
        if (false === ($config = $this->loadConfigFile())) {
            $this->outputAndExit('Invalid json in config/profile.json');
        }
        return $this->parser->endpoint ? $this->parser->endpoint : $config['default']['endpoint'];
    }

    private function authorize($options)
    {
        // We will support three legged oauth soon
        if (count($options) < 1) {
            $usage = <<<EOF
Usage: authorize <grant_type> <client_id> [....]

    grant_type - (required) A compatible grant type, one of: ["password", "code"]

Providing a username/password will return a token immediately.  Excluding these will
provide a URL to retrieve the authorization code. Clients can be registered at
https://developer.omniture.com/en_US/devcenter/applications

EOF;
            $this->outputAndExit($usage);
        }

        $grant_type = array_shift($options);

        if ($grant_type == 'code') {
            if (count($options) < 1) {
                $usage = <<<EOF
Usage: authorize code <client_id> [<client_secret> <username> <password>]

    client_id - (required) The ID of your registered client

The "code" grant returns a URL to retrieve the authorization code.
Clients can be registered at
https://developer.omniture.com/en_US/devcenter/applications

EOF;
                $this->outputAndExit($usage);
            }

            $clientId = array_shift($options);
            $endpoint = $this->getEndpoint();
            $instructions = <<<EOF
Paste the following URL in your browser, and then copy the authorization
code returned by the server:

    https://%s/authorize?client_id=%s


EOF;

            echo sprintf($instructions, $endpoint, urlencode($clientId));
            // provide prompt for authorization code here

        } else if ($grant_type == 'password') {
            if (count($options) < 4) {
                $usage = <<<EOF
Usage: authorize password <client_id> <client_secret> <username> <password>

    client_id     - (required) The ID of your registered client
    client_secret - (required) The secret of your registered client
    username      - (required) username of the user to authorize
    password      - (required) password to authorize

The "password" grant provides a token immediately.
Clients can be registered at
https://developer.omniture.com/en_US/devcenter/applications

EOF;
                $this->outputAndExit($usage);
            }
            list($clientId, $clientSecret, $username, $password) = $options;

            $endpoint = $this->getEndpoint();

            $auth = new AdobeDigitalMarketing_Auth_HttpBasic();
            $adm = new AdobeDigitalMarketing_Client(new AdobeDigitalMarketing_HttpClient_Curl(array('endpoint' => $endpoint) + $config['default'], $auth));

            $adm->authenticate($clientId, $clientSecret);

            if (!$tokenData = $adm->getOAuthApi()->getTokenFromUserCredentials($username, $password)) {
                $response = $adm->getLastResponse();
                $error = null;

                if (($json = json_decode($response['response'], true)) != false) {
                    $response = $json;
                }

                if (isset($response['errorMessage']) && !empty($response['errorMessage'])) {
                    $error =$response['errorMessage'];
                }
                if (isset($response['error']['message'])) {
                    $error = $response['error']['message'];
                }

                if ($error && $error == 'invalid_client') {
                    $this->outputAndExit('Error: Invalid client credentials');
                }
                $this->outputAndExit('Error: '.print_r($response, 1));
            }
            $token = $tokenData['access_token'];

            // set defaults if config is new
            if (!isset($config[$clientId])) {
                $config[$clientId] = array();
            }
            if (!isset($config[$clientId][$endpoint])) {
                $config[$clientId][$endpoint] = array('tokens' => array(), 'default' => '');
            }

            // check if token exists
            if (!in_array($token, $config[$clientId][$endpoint]['tokens'])) {
                $config[$clientId][$endpoint]['tokens'][] = $token;
            }

            // clear the defaults across all clients for this endpoint
            foreach ($config as $id => $conf) {
                unset($config[$id][$endpoint]['default']);
            }

            // set the new token as default
            $config[$clientId][$endpoint]['default'] = $token;

            $this->writeConfigFile($config);

            $this->outputAndExit('Token: '.$this->formatJson(json_encode($tokenData)));
        } else {
            $this->outputAndExit("Unrecognized grant type \"$grant_type\", must be one of [code, password]");
        }
    }

    public function profile($options)
    {
        $config = $this->loadConfigFile();
        $profile = 'default';

        // show all profile options
        if (count($options) == 0) {
            $this->outputAndExit(sprintf("%s values:\n%s", $profile, $this->formatJson(json_encode($config['default']))));
        }
        // get method
        if (count($options) == 1) {
            if (isset($config[$profile][$options[0]])) {
                $this->outputAndExit(sprintf('%s value for "%s": %s', $profile, $options[0], $config[$profile][$options[0]]));
            }
            $this->outputAndExit(sprintf('%s value for "%s" is not set', $profile, $options[0]));
        }
        // set method
        if (count($options) > 3) {
            $this->outputAndExit('Error: too many arguments to "profile" command');
        }

        $config[$profile][$options[0]] = $options[1];
        $this->writeConfigFile($config);
        $this->outputAndExit(sprintf('%s value for "%s" set to %s', $profile, $options[0], $options[1]));
    }

    private function getDefaultConfigFile()
    {
        // come up with a better default in the future - create a new endpoint?
        $config = array(
            'default' => array('endpoint' => 'api.omniture.com'),
        );

        return $config;
    }

    private function loadConfigFile()
    {
        $clientFile = dirname(__FILE__).'/../../config/profile.json';
        if (file_exists($clientFile)) {
            return json_decode(file_get_contents($clientFile), 1);
        }
        $this->outputAndExit('Error: Please copy "config/profile.json.dist" to "config/profile.json"');
    }

    private function writeConfigFile($config)
    {
        $configFile = dirname(__FILE__).'/../../config/profile.json';
        file_put_contents($configFile, $this->formatJson(json_encode($config)));
    }

    private function outputAndExit($output)
    {
        echo $output . "\n";
        exit;
    }

    /**
     * Indents a flat JSON string to make it more human-readable.
     *
     * @param string $json The original JSON string to process.
     *
     * @return string Indented version of the original JSON string.
     */
    private function formatJson($json) {

        $result      = '';
        $pos         = 0;
        $strLen      = strlen($json);
        $indentStr   = '  ';
        $newLine     = "\n";
        $prevChar    = '';
        $outOfQuotes = true;

        for ($i=0; $i<=$strLen; $i++) {

            // Grab the next character in the string.
            $char = substr($json, $i, 1);

            // Are we inside a quoted string?
            if ($char == '"' && $prevChar != '\\') {
                $outOfQuotes = !$outOfQuotes;

            // If this character is the end of an element,
            // output a new line and indent the next line.
            } else if(($char == '}' || $char == ']') && $outOfQuotes) {
                $result .= $newLine;
                $pos --;
                for ($j=0; $j<$pos; $j++) {
                    $result .= $indentStr;
                }
            }

            // Add the character to the result string.
            $result .= $char;

            // If the last character was the beginning of an element,
            // output a new line and indent the next line.
            if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                $result .= $newLine;
                if ($char == '{' || $char == '[') {
                    $pos ++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            $prevChar = $char;
        }

        return $result;
    }
}
