<?php

interface AdobeDigitalMarketing_AuthInterface
{
    public function setAuthHeadersAndParameters(array $headers, array $parameters, array $options = array());
}