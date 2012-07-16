# PHP Adobe Digital Marketing API

A simple, Object Oriented API wrapper for the AdobeDigitalMarketing Api written with PHP5.  
This library is modeled after the [php-github-api](https://github.com/ornicar/php-github-api) library built by [ornicar](https://github.com/ornicar)

Uses [AdobeDigitalMarketing API 1.3](http://developer.omniture.com/en_US/documentation).

Requires

 * PHP 5.2 or higher
 * [php curl](http://php.net/manual/en/book.curl.php) but it is possible to write another transport layer..

If the method you need does not exist yet, dont hesitate to request it with an [issue](http://github.com/Adobe-Digital-Marketing/php-adm-api/issues)!

## Autoload

The first step to use php-adm-api is to register its autoloader:

    require_once '/path/to/php-adm-api/lib/AdobeDigitalMarketing/Autoloader.php';
    AdobeDigitalMarketing_Autoloader::register();

Replace the `/path/to/php-adm-api/` path with the path you used for php-adm-api installation.

> php-adm-api follows the PEAR convention names for its classes, which means you can easily integrate php-adm-api classes loading in your own autoloader.

## Instantiate a new AdobeDigitalMarketing Client

    $adm = new AdobeDigitalMarketing_Client();

From this object you can now access all of the different AdobeDigitalMarketing APIs (listed below)

### Authenticate a user

Authenticate using your Adobe Digital Marketing Web Services username and secret.  You can obtain one by logging into the [Digital Marketing Suite](https://my.omniture.com) and browsing to **Admin** > **Company** > **Web Services**

    $adm->authenticate($username, $secret);

### Deauthenticate a user

Cancels authentication.

    $adm->deAuthenticate();

Next requests will not be authenticated

## Reports

For queueing SiteCatalyst reports  
Wraps [SiteCatalyst Report API](http://developer.omniture.com/en_US/documentation/sitecatalyst-reporting).

    $reportApi = $adm->getReportApi();

### Search for artists by name

    $results = $adm->getReportApi()->queueTrended(array(
        'reportSuiteID' => 'YOUR-REPORT-SUITE-ID', 
        'date'     => date('Y-m-d'),
        'metrics'  => array('pageviews'),
        'elements' => array('eVar1'),
    ));
    
    print_r($results);

    Array
    (
      [status]    => ready
      [statusMsg] => Your report has been queued
      [report_id] => #######
    )

Returns an array of results as described in [the documentation](https://developer.omniture.com/en_US/documentation/sitecatalyst-reporting/r-reportqueueresponse)

# To Do

Better documentation and test coverage will be coming soon
