<?php

namespace AdobeMarketingCloud\Auth;

use AdobeMarketingCloud\AuthInterface;

/**
 * Performs requests on AdobeMarketingCloud API. API documentation should be self-explanatory.
 *
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class Wsse implements AuthInterface
{
    private $username;
    private $secret;
    private $algorithm;

    public function authenticate($username, $secret, $algorithm = null)
    {
        $this->username = $username;
        $this->secret = $secret;
        $this->algorithm = $algorithm;
    }

    public function setAuthHeadersAndParameters(array $headers, array $parameters, array $options = array())
    {
        if(!$this->username || !$this->secret) {
            throw new Exception("username and secret must be set before making a request");
        }

        $nonce = $this->getNonce();
        $created = gmdate('c');

        if (empty($this->algorithm)) {
            $algorithm = 'sha1';
        } else {
            $algorithm = $this->algorithm;
        }

        $digest = base64_encode(hash($algorithm,$nonce.$created.$this->secret,true));
        $b64nonce = base64_encode($nonce);

        $header = sprintf('X-WSSE: UsernameToken Username="%s", PasswordDigest="%s", Nonce="%s", Created="%s"',
            $this->username,
            $digest,
            $b64nonce,
            $created
        );

        if (!empty($this->algorithm)) {
            $header = sprintf('%s, Algorithm="%s"', $header, $this->algorithm);
        }

        $headers[] = $header;

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
