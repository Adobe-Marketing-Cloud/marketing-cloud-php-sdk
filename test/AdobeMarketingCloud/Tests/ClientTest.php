<?php

namespace AdobeMarketingCloud\Tests;

use AdobeMarketingCloud\Client;
use AdobeMarketingCloud\Auth;
use AdobeMarketingCloud\Api;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testInstanciateWithoutHttpClient()
    {
        $client = new Client();

        $this->assertInstanceOf('AdobeMarketingCloud\HttpClientInterface', $client->getHttpClient());
    }

    public function testInstanciateWithHttpClient()
    {
        $httpClient = $this->getHttpClientMock();
        $client = new Client($httpClient);

        $this->assertEquals($httpClient, $client->getHttpClient());
    }

    public function testAuthenticate()
    {
        $username = 'username';
        $secret   = 'secret';

        $httpClient = $this->getHttpClientMock();
        $authMock   = $this->getAuthServiceMock();
        $httpClient->expects($this->once())
            ->method('getAuthService')
            ->with()
            ->will($this->returnValue($authMock));
        $client = $this->getClientMockBuilder()
            ->setMethods(array('getHttpClient'))
            ->getMock();
        $client->expects($this->once())
            ->method('getHttpClient')
            ->with()
            ->will($this->returnValue($httpClient));

        $client->authenticate($username, $secret);
    }

    public function testDeauthenticate()
    {
        $client = $this->getClientMockBuilder()
            ->setMethods(array('authenticate'))
            ->getMock();
        $client->expects($this->once())
            ->method('authenticate')
            ->with(null, null);

        $client->deAuthenticate();
    }

    public function testGet()
    {
        $path      = '/some/path';
        $parameters = array('a' => 'b');
        $options    = array('c' => 'd');

        $httpClient = $this->getHttpClientMock();
        $httpClient->expects($this->once())
            ->method('get')
            ->with($path, $parameters, $options);

        $client = $this->getClientMockBuilder()
            ->setMethods(array('getHttpClient'))
            ->getMock();
        $client->expects($this->once())
            ->method('getHttpClient')
            ->with()
            ->will($this->returnValue($httpClient));

        $client->get($path, $parameters, $options);
    }

    public function testPost()
    {
        $path      = '/some/path';
        $parameters = array('a' => 'b');
        $options    = array('c' => 'd');

        $httpClient = $this->getHttpClientMock();

        $httpClient->expects($this->once())
            ->method('post')
            ->with($path, $parameters, $options);
        $client = $this->getClientMockBuilder()
            ->setMethods(array('getHttpClient'))
            ->getMock();
        $client->expects($this->once())
            ->method('getHttpClient')
            ->with()
            ->will($this->returnValue($httpClient));

        $client->post($path, $parameters, $options);
    }

    public function testDefaultApi()
    {
        $client = new Client();

        $this->assertInstanceOf('AdobeMarketingCloud\Api\Report', $client->getReportApi());
    }

    public function testInjectApi()
    {
        $client = new Client();

        $reportApiMock = $this->getMockBuilder('AdobeMarketingCloud\ApiInterface')
            ->getMock();

        $client->setApi('report', $reportApiMock);

        $this->assertSame($reportApiMock, $client->getReportApi());
    }

    protected function getClientMockBuilder()
    {
        return $this->getMockBuilder('AdobeMarketingCloud\Client')
            ->disableOriginalConstructor();
    }

    protected function getHttpClientMock()
    {
        $httpMock = $this->getMockBuilder('AdobeMarketingCloud\HttpClientInterface')
            ->getMock();

        return $httpMock;
    }

    protected function getAuthServiceMock()
    {
        $authMock = $this->getMockBuilder('AdobeMarketingCloud\Auth\Wsse')
            ->getMock();
        return $authMock;
    }
}
