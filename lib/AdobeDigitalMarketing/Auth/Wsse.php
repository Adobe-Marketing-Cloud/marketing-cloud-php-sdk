<?php

/**
 * Performs requests on AdobeDigitalMarketing API. API documentation should be self-explanatory.
 *
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class AdobeDigitalMarketing_Auth_Wsse implements AdobeDigitalMarketing_AuthInterface
{
    private $username;
    private $secret;

    public function authenticate($username, $secret)
    {
        $this->username = $username;
        $this->secret = $secret;
    }

    public function setAuthHeadersAndParameters(array $headers, array $parameters, array $options = array())
    {
        if(!$this->username || !$this->secret) {
            throw new AdobeDigitalMarketing_Auth_Exception("username and secret must be set before making a request");
        }

        $nonce = $this->getNonce();
        $created = gmdate('c');

        $digest = base64_encode(sha1($nonce.$created.$this->secret,true));
        $b64nonce = base64_encode($nonce);

        $headers[] = sprintf('X-WSSE: UsernameToken Username="%s", PasswordDigest="%s", Nonce="%s", Created="%s"',
          $this->username,
          $digest,
          $b64nonce,
          $created
        );

        return array($headers, $parameters);
    }

    /**
     * Uses UUID Version 4
     * @see http://en.wikipedia.org/wiki/Universally_unique_identifier
     */
    protected function getNonce()
    {
        return sprintf('%04x%04x-%04x-%03x4-%04x-%04x%04x%04x',
            mt_rand(0, 65535), mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 4095),
            bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
            mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)
        );
    }
}
