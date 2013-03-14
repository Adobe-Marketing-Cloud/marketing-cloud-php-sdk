# Adobe Marketing Cloud PHP SDK
[![Build Status](https://secure.travis-ci.org/Adobe-Marketing-Cloud/marketing-cloud-php-sdk.png)](http://travis-ci.org/Adobe-Marketing-Cloud/marketing-cloud-php-sdk)

A simple, Object Oriented wrapper for the Adobe Marketing Cloud APIs written in PHP5.
This library is modeled after the [php-github-api](https://github.com/ornicar/php-github-api) library built by [ornicar](https://github.com/ornicar)

Uses the [Adobe Marketing Cloud APIs](http://developer.omniture.com/en_US/documentation). Default version is 1.3, but 1.2 is compatible.

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

Authenticate using your Adobe Digital Marketing Web Services username and secret.  You can obtain one by logging into the [Marketing Cloud](https://my.omniture.com) and browsing to **Admin** > **Company** > **Web Services**

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

## Calling Additional Methods

All the methods visible in the [API Explorer](https://developer.omniture.com/en_US/get-started/api-explorer) can be called
using the `SuiteApi` class.  This is a generic class that accepts the method name and parameters, and will return the json
encoded response of the result.

    $adm->getSuiteApi()->post('Saint.ExportCreateJob', $parameters);

## Curl Debugging

If a request returns null, call the `getLastResponse` method on the client in order to see the curl information and raw response:

    if (!$response = $adm->getReportApi()->getReport($reportId)) {
      print_r($adm->getLastResponse());
    }

Passing in the debug flag to the HttpClient will output the response automatically when the response does not exist, or is not in json format

    $adm = new AdobeDigitalMarketing_Client(
      new AdobeDigitalMarketing_HttpClient_Curl(array('debug' => true))
    );

## Command Line Utility

> OAuth is not yet available for production use.  This functionality is still under development.

The easiest way to begin with OAuth is by using the command line utility tool (`bin/adm`).

To get started, copy over the configuration file:

    $ cp config/profile.json.dist config/profile.json

Once this is done, run the `adm` command to get started

    $ ./bin/adm

    Calls the Adobe Marketing Cloud APIs
    To get started, call

        $ adm authorize

    to retrieve a token.  Some other options avialable are

     -h, --help      Display a help message and exit
     -v, --version   Display the current api version
     -e, --endpoint  Specify the api endpoint

    See developer.omniture.com for more information

The first step will be to get an oauth token.  This can be accomplished by providing the `authorize` command with a client id, client secret, username and password.  Client IDs can be created in the [Developer Connection](https://developer.omniture.com/en_US/devcenter/applications).  Once you've done this, run the `authorize` command to receive a token:

    $ adm authorize CLIENT_ID CLIENT_SECRET USERNAME PASSWORD
    Token: {
      "access_token":"f9f071ca2c25d23eff01a1ea238d6f85666be0f6",
      "expires_in":2592000,
      "token_type":"bearer",
      "scope":null,
      "success":true
    }

You've now received your first access token.  This has been saved to `config/profile.json`, so you don't need to worry about it.  You can go ahead and start making requests!

    $ adm request Company.GetReportSuites
    Array
    (
       [report_suites] => Array
           (
               [0] => Array
                   (
                       [rsid] => your.rsid
                       [site_title] => Your Site
                   )
               ...
           )
    )


### Endpoints

The default endpoint is **api.omniture.com**.  If you are experiencing issues, call the `Company.GetEndpoint` method to find out which endpoint you should be using:

    $ ./bin/adm Company.GetEndpoint 'company=My Company'
    https://api2.omniture.com/admin/1.3/rest/

In this case, the endpoint is *api2.omniture.com*.  To use this endpoint instead, set the endpoint in your profile:

    $ ./bin/adm profile endpoint api2.omniture.com
    default value for "endpoint" set to api2.omniture.com

Now all subsequent requests will use this endpoint.  If you need to use an endpoint other than the default, you can pass this in with the `-e` or `--endpoint` options

    $ ./bin/adm authorize CLIENT_ID CLIENT_SECRET USERNAME PASSWORD --endpoint 'api3.omniture.com'

## To Do

Better documentation and test coverage will be coming soon
