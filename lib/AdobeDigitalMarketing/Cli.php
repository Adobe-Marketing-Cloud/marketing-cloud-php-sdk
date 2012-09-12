<?php

/**
*
*/
class AdobeDigitalMarketing_Cli
{
    const VERSION_MAJOR = 0;
    const VERSION_MINOR = 1;
    const VERSION_PATCH = 0;

    private $supported_commands = array('authorize', 'request');
    private $parser;

    public function __construct()
    {
        $this->parser = new AdobeDigitalMarketing_OptionParser();
        $this->parser->addHead("\nCalls the Adobe Digital Marketing Suite APIs");
        $this->parser->addHead("\nTo get started, call \n");
        $this->parser->addHead("\n\t$ adm authorize\"\n");
        $this->parser->addHead("\nto retrieve a token.  Some other options avialable are\n");
        $this->parser->addRule('h|help', "Display a help message and exit");
        $this->parser->addRule('v|version', "Display the current api version\n");
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
            $this->outputAndExit($parser->getUsage());
        }

        if ($this->parser->version) {
            $this->outputAndExit('Version '.implode('.', array(self::VERSION_MAJOR, self::VERSION_MINOR, self::VERSION_PATCH)));
        }

        if (!in_array($command = $options[0], $this->supported_commands)) {
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
                $this->request($options);
                break;
            case 'authorize':
                $this->authorize($options);
                break;
        }
    }

    public function request($options)
    {
        $defaultConf = $this->loadDefaultConfigFile();
        $token = null;

        // grab the default token
        if (count($clientConf = $this->loadClientConfigFile()) > 0) {
            foreach ($clientConf as $clientId => $config) {
                if (isset($config['default'])) {
                    $token = $config['default'];
                    break;
                }
                if (isset($config['tokens'])) {
                    // grab the last token to use as a final default
                    $token = array_pop($config['tokens']);
                }
            }
        }

        if (!$token) {
            $this->outputAndExit('Error: use "authenticate" method to store your credentials before making a request');
        }

        $auth = new AdobeDigitalMarketing_Auth_OAuth2();
        $adm = new AdobeDigitalMarketing_Client(new AdobeDigitalMarketing_HttpClient_Curl($config + $defaultConf['default'], $auth));

        $adm->authenticate($token);

        if (count($options) < 1) {
         $this->outputAndExit('Error: you must supply the method you want to call as the first argument to "request"');
        }

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
            print_r($response);
        } else {
            print_r($adm->getLastResponse());
        }
    }

    private function authorize($options)
    {
        // We will support three legged oauth soon
        if (count($options) < 4) {
            $this->outputAndExit("Usage: authorize [clientId] [clientSecret] [username] [password]");
        }

        list($clientId, $clientSecret, $username, $password) = $options;

        $defaultConf = $this->loadDefaultConfigFile();
        $clientConf = $this->loadClientConfigFile();
        $config = (isset($clientConf[$clientId]) ? (array) $clientConf[$clientId] : array()) + (array) $defaultConf['default'];
        $auth = new AdobeDigitalMarketing_Auth_HttpBasic();
        $adm = new AdobeDigitalMarketing_Client(new AdobeDigitalMarketing_HttpClient_Curl($config, $auth));

        $adm->authenticate($clientId, $clientSecret);

        if (!$token = $adm->getOAuthApi()->getTokenFromUserCredentials($username, $password)) {
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

            if ($error && $error = 'invalid_client') {
                $this->outputAndExit('Error: Invalid client credentials');
            }
            $this->outputAndExit('Error: '.print_r($response, 1));
        }

        if (isset($clientConf[$clientId]['tokens'])) {
            if (!in_array($token, $clientConf[$clientId]['tokens'])) {
                $clientConf[$clientId]['tokens'][] = $token;
            }
        } else {
            $clientConf[$clientId] = array('tokens' => array($token));
        }

        // clear the default
        foreach ($clientConf as $id => $conf) {
            unset($clientConf[$id]['default']);
        }

        // set the new token as default
        $clientConf[$clientId]['default'] = $token;

        $this->writeClientConfigFile($clientConf);

        $this->outputAndExit('Token: '.$token);
    }

    private function loadDefaultConfigFile($clientId = null)
    {
        $defaultFile = dirname(__FILE__).'/../../config/default.json';
        return json_decode(file_get_contents($defaultFile), 1);
    }

    private function loadClientConfigFile()
    {
        $clientFile = dirname(__FILE__).'/../../config/profile.json';
        if (file_exists($clientFile)) {
            return json_decode(file_get_contents($clientFile), 1);
        }
        return array();
    }

    private function writeClientConfigFile($config)
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