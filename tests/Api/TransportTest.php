<?php
namespace SergioCarlos\IBMWatson\WatsonLanguageTranslator\Translator\Tests;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Response;
use SergioCarlos\IBMWatson\WatsonLanguageTranslator\Api\Transport;

class TransportTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiateTransportWithoutSpecifyingHTTPClient()
    {
        $this->assertInstanceOf(
            Transport::class,
            new Transport('username', 'password')
        );
    }

    /**
     * @dataProvider provideSendSynchronousApiRequestKicksOffHTTPClientTestData
     * @param string $requestBody
     * @param bool $isRequestBodyJsonFlag
     */
    public function testSendSynchronousApiRequestKicksOffExpectedRequestOnHTTPClient(
        $requestBody,
        $isRequestBodyJsonFlag
    ) {
        $httpMethod = 'GET';
        $apiUri = 'whatever';
        $username = 'username';
        $password = 'password';
        $httpClient = $this->getMock(
            HttpClient::class,
            [
                'request'
            ]
        );
        $expectedHeaderContentType = ($isRequestBodyJsonFlag)
            ? 'application/json'
            : 'text/plain';
        $expectedHeadersArray = [
            'auth' => [$username, $password],
            'body' => $requestBody,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => $expectedHeaderContentType
            ],
        ];
        $httpClient->expects($this->once())
            ->method('request')
            ->with($httpMethod, $apiUri, $expectedHeadersArray)
            ->will($this->returnValue(new Response()));
        $transport = new Transport($username, $password, $httpClient);
        $transport->sendSynchronousApiRequest($httpMethod, $apiUri, $requestBody, $isRequestBodyJsonFlag);
    }

    /**
     * @return array
     */
    public function provideSendSynchronousApiRequestKicksOffHTTPClientTestData()
    {
        return [
            'JSON request' => [json_encode(new \stdClass()), true],
            'Plain text request' => [':)', false]
        ];
    }
}