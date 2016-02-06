<?php
namespace SergioCarlos\IBMWatson\WatsonLanguageTranslator\Api;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException as HttpClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\RequestOptions as HTTPRequestOptions;
use SergioCarlos\IBMWatson\WatsonLanguageTranslator\Exception\Api as ApiException;
use SergioCarlos\IBMWatson\WatsonLanguageTranslator\Exception\Client as ClientException;
use SergioCarlos\IBMWatson\WatsonLanguageTranslator\Exception\Transport as TransportException;
use SergioCarlos\IBMWatson\WatsonLanguageTranslator\Exception\Unauthorized as UnauthorizedException;

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
     * @throws TransportException
     */
    public function sendSynchronousApiRequest(
        $httpMethod,
        $apiUri,
        $requestBody,
        $jsonRequestBodyFlag = false
    ) {
        try {
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
        } catch (HttpClientException $e) {
            $this->handleClientException($e);
        } catch (ServerException $e) {
            throw new ApiException($e->getMessage(), 0 , $e);
        } catch (\Exception $e) {
            throw new TransportException("Unexpected transport error.", 0, $e);
        }
    }

    /**
     * @param HttpClientException $e
     * @throws TransportException
     */
    protected function handleClientException(HttpClientException $e)
    {
        switch ($e->getResponse()->getStatusCode()) {
            case 401:
                throw new UnauthorizedException("Unauthorized API request.", 0, $e);
            default:
                throw new ClientException($e->getMessage(), 0, $e);
        }
    }
}
