<?php

interface AdobeDigitalMarketing_AuthInterface
{
    public function setAuthHeadersAndParameters(array $headers, array $parameters, array $options = array());

    // You will also need to define an authenticate() function for each auth class, not defined here because
    // an interface does not support a variable number of parameters
    // public function authenticate();
}