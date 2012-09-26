<?php

class AdobeDigitalMarketing_Tests_ClientTest extends PHPUnit_Framework_TestCase
{
    public function testInstanciateWithoutHttpClient()
    {
        $client = new AdobeDigitalMarketing_Client();

        $this->assertInstanceOf('AdobeDigitalMarketing_HttpClientInterface', $client->getHttpClient());
    }

    public function testInstanciateWithHttpClient()
    {
        $httpClient = $this->getHttpClientMock();
        $client = new AdobeDigitalMarketing_Client($httpClient);

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
        $client = new AdobeDigitalMarketing_Client();

        $this->assertInstanceOf('AdobeDigitalMarketing_Api_Report', $client->getReportApi());
    }

    public function testInjectApi()
    {
        $client = new AdobeDigitalMarketing_Client();

        $reportApiMock = $this->getMockBuilder('AdobeDigitalMarketing_ApiInterface')
            ->getMock();

        $client->setApi('report', $reportApiMock);

        $this->assertSame($reportApiMock, $client->getReportApi());
    }

    protected function getClientMockBuilder()
    {
        return $this->getMockBuilder('AdobeDigitalMarketing_Client')
            ->disableOriginalConstructor();
    }

    protected function getHttpClientMock()
    {
        $httpMock = $this->getMockBuilder('AdobeDigitalMarketing_HttpClientInterface')
            ->getMock();

        return $httpMock;
    }

    protected function getAuthServiceMock()
    {
        $authMock = $this->getMockBuilder('AdobeDigitalMarketing_Auth_Wsse')
            ->getMock();
        return $authMock;
    }
}
