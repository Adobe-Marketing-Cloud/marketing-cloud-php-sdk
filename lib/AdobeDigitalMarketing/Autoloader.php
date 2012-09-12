<?php

/**
 * Autoloads AdobeDigitalMarketing classes
 *
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class AdobeDigitalMarketing_Autoloader
{
    /**
     * Registers AdobeDigitalMarketing_Autoloader as an SPL autoloader.
     */
    static public function register()
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(new self, 'autoload'));
    }

    /**
     * Handles autoloading of classes.
     *
     * @param  string  $class  A class name.
     *
     * @return boolean Returns true if the class has been loaded
     */
    static public function autoload($class)
    {
        if (0 !== strpos($class, 'AdobeDigitalMarketing')) {
            return;
        }

        if (file_exists($file = dirname(__FILE__).'/../'.str_replace('_', '/', $class).'.php')) {
            require $file;
        }
    }
}
