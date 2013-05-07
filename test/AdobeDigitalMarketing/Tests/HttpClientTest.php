<?php

class AdobeDigitalMarketing_Tests_HttpClientTest extends PHPUnit_Framework_TestCase
{
    public function testInstanciateWithOptions()
    {
        $httpClient = new AdobeDigitalMarketing_HttpClient_TestDriver(array(
            'timeout' => 33
        ));

        $this->assertEquals(33, $httpClient->getOption('timeout'));
        $this->assertEquals(443, $httpClient->getOption('http_port'));
        $this->assertTrue($httpClient->getOption('follow-location'));
    }

    public function testGet()
    {
        $path       = '/some/path';
        $parameters = array('a' => 'b');
        $options    = array('c' => 'd');

        $httpClient = $this->getHttpClientMockBuilder()
            ->setMethods(array('doRequest', 'request'))
            ->getMock();
        $httpClient->expects($this->once())
            ->method('request')
            ->with($path, $parameters, 'GET', $options);

        $httpClient->get($path, $parameters, $options);
    }

    public function testPost()
    {
        $path       = '/some/path';
        $parameters = array('a' => 'b');
        $options    = array('c' => 'd');

        $httpClient = $this->getHttpClientMockBuilder()
            ->setMethods(array('doRequest', 'request'))
            ->getMock();
        $httpClient->expects($this->once())
            ->method('request')
            ->with($path, $parameters, 'POST', $options);

        $httpClient->post($path, $parameters, $options);
    }

    public function testRequest()
    {
        $path       = '/some/path';
        $parameters = array('a' => 'b');
        $method     = 'GET';
        $options    = array('c' => 'd');

        $httpClient = $this->getHttpClientMockBuilder()
            ->setMethods(array('doRequest', 'decodeResponse'))
            ->getMock();
        $httpClient->expects($this->once())
            ->method('doRequest')
            ->will($this->returnValue(array()));
        $httpClient->expects($this->once())
            ->method('decodeResponse')
            ->with(array())
            ->will($this->returnValue(array()));

        $response = $httpClient->request($path, $parameters, $method, $options);

        $this->assertSame(array(), $response);
    }

    protected function getHttpClientMockBuilder()
    {
        return $this->getMockBuilder('AdobeDigitalMarketing_HttpClient');
    }
}

class AdobeDigitalMarketing_HttpClient_TestDriver extends AdobeDigitalMarketing_HttpClient
{
    protected function doRequest($url, array $parameters = array(), $httpMethod = 'GET', array $options = array())
    {
    }

    /**
     * Get an option value.
     *
     * @param  string $name The option name
     *
     * @return mixed  The option value
     */
    public function getOption($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }
}