# Adobe Digital Marketing PHP SDK

A simple, Object Oriented wrapper for the Adobe Digital Marketing Suite APIs written in PHP5.
This library is modeled after the [php-github-api](https://github.com/ornicar/php-github-api) library built by [ornicar](https://github.com/ornicar)

Uses the [Adobe Digital Marketing Suite APIs](http://developer.omniture.com/en_US/documentation). Default version is 1.3, but 1.2 is compatible.

Requires

 * PHP 5.2 or higher
 * [php curl](http://php.net/manual/en/book.curl.php) but it is possible to write another transport layer..

If the method you need does not exist yet, dont hesitate to request it with an [issue](http://github.com/Adobe-Digital-Marketing/adobe-digital-marketing-php-sdk/issues)!

## Autoload

The first step to use adobe-digital-marketing-php-sdk is to register its autoloader:

    require_once '/path/to/sdk/lib/AdobeDigitalMarketing/Autoloader.php';
    AdobeDigitalMarketing_Autoloader::register();

Replace the `/path/to/sdk/` path with the path you used for adobe-digital-marketing-php-sdk installation.

> This SDK follows the PEAR convention names for its classes, which means you can easily integrate the class loading in your own autoloader.

## Instantiate a new AdobeDigitalMarketing Client

    $adm = new AdobeDigitalMarketing_Client();

From this object you can now access all of the different AdobeDigitalMarketing APIs (listed below)

### Authenticate a user

Authenticate using your Adobe Digital Marketing Web Services username and secret.  You can obtain one by logging into the [Digital Marketing Suite](https://my.omniture.com) and browsing to **Admin** > **Company** > **Web Services**

    $adm->authenticate($username, $secret);

**Note**: If authentication fails, it's probably because your company endpoint is different from the default.  To verify, this, call the `getEndpoint()` method:

    echo $adm->getCompanyApi()->getEndpoint('My Company Name');
    // "https://api2.omniture.com"

If the return value of this function is different from *https:api.omniture.com*, you will need to set the endpoint in your client

    $adm->setEndpoint('https://api2.omniture.com');

### Deauthenticate a user

Cancels authentication.

    $adm->deAuthenticate();

Next requests will not be authenticated

## Reports

For queueing SiteCatalyst reports
Wraps [SiteCatalyst Report API](http://developer.omniture.com/en_US/documentation/sitecatalyst-reporting).

    $reportApi = $adm->getReportApi();

### Run a Ranked Report

    $response = $reportApi->queueRanked(array(
        'reportSuiteID' => 'your-id',
        'date'     => date('Y-m-d'),
        'metrics'  => array(
            array('id' => 'pageviews'),
        ),
        'elements' => array(
            array('id' => 'eVar1'),
        ),
    ));

    print_r($response);

The above code will render the status of your queued report, which will look something like this:

    Array
    (
      [status]    => ready
      [statusMsg] => Your report has been queued
      [reportID] => 123456789
    )

### Retrieve a Queued Report

Once the report ID is retrieved for the trended, ranked, or overtime report, use the Report.GetReport API call to retrieve the report

    $response = $reportApi->queueRanked(array(
        //... (see above)
    ));

    $reportId = $response['reportID'];

    do {
        $report = $reportApi->getReport($reportId);
        sleep(2);
    } while ($report['status'] == 'not ready');

    print_r($report['report']);

The above code will render the Report array, which will look something like this:

    Array
    (
        [reportSuite] => Array (
            [id]      => your-id
            [name]    => Your Report Suite
        )
        [period]      => "Mon. 16 July 2012",
        [elements]    => Array (
            [id]      => eVar2,
            [name]    => eVar 2
        )
        [metrics]     => Array (
            [id]      => event2
            [name]    => Page View Event
            [type]    => number
        )
        [type]        => trended
        [data]        => Array (
            ...
        )
        [totals]      => Array (
            ...
        )
    )

Returns an array of results as described in [the documentation](https://developer.omniture.com/en_US/documentation/sitecatalyst-reporting/r-reportqueueresponse)

# To Do

Better documentation and test coverage will be coming soon
