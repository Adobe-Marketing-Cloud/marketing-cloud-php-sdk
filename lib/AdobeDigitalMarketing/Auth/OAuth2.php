<?php

/**
 * Performs requests on AdobeDigitalMarketing API. API documentation should be self-explanatory.
 *
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class AdobeDigitalMarketing_Auth_OAuth2 implements AdobeDigitalMarketing_AuthInterface
{
    public function addAuthHeaders($headers, $options)
    {
        $username = $options['username'];
        $secret   = $options['secret'];
        $nonce = md5(rand());
        $created = gmdate('Y-m-d H:i:s T');

        $digest = base64_encode(sha1($nonce.$created.$secret,true));
        $b64nonce = base64_encode($nonce);

        $headers[] = sprintf('X-WSSE: UsernameToken Username="%s", PasswordDigest="%s", Nonce="%s", Created="%s"',
          $username,
          $digest,
          $b64nonce,
          $created
        );
        
        return $headers;
    }
}