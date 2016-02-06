<?php
namespace SergioCarlos\IBMWatson\WatsonLanguageTranslator\Api;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\RequestOptions as HTTPRequestOptions;

/**
 * This client deals with the low level aspects of interacting with Watson's API.
 * It serves as an encapsulation layer when interacting with the actual HTTP transport.
 */
class Transport
{
    const API_URL = 'https://gateway.watsonplatform.net/language-translation/api/v2/';

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @param string $username
     * @param string $password
     * @param HttpClient|null $httpClient
     */
    public function __construct($username, $password, HttpClient $httpClient = null)
    {
        $this->username = $username;
        $this->password = $password;

        if ($httpClient === null) {
            $httpClient = new HttpClient([
                'base_uri' => static::API_URL
            ]);
        }

        $this->httpClient = $httpClient;
    }

    /**
     * @param string $httpMethod
     * @param string $apiUri
     * @param string $requestBody
     * @param bool $jsonRequestBodyFlag
     * @return mixed
     */
    public function sendSynchronousApiRequest(
        $httpMethod,
        $apiUri,
        $requestBody,
        $jsonRequestBodyFlag = false
    ) {
        $response = $this->httpClient->request(
            $httpMethod,
            $apiUri,
            [
                HTTPRequestOptions::AUTH => [$this->username, $this->password],
                HTTPRequestOptions::BODY => $requestBody,
                HTTPRequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                    'Content-Type' => ($jsonRequestBodyFlag)
                        ? 'application/json'
                        : 'text/plain',
                ],
            ]
        );

        return json_decode($response->getBody()->getContents());
    }
}
