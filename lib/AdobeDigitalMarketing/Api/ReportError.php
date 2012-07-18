<?php

/**
 * Report error
 *
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class AdobeDigitalMarketing_Api_ReportError extends Exception
{
    public function __construct($message, $code, Exception $previous = null)
    {
        $code = (int) str_replace('error ', '', $code);
        parent::__construct($message, $code, $previous);
    }
}