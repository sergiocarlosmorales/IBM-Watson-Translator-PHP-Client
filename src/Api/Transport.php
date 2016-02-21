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
    const MIME_TYPE_PLAIN_TEXT = 'text/plain';
    const MIME_TYPE_JSON = 'application/json';

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
     * @return mixed
     */
    public function sendSynchronousPlainTextApiRequest($httpMethod, $apiUri, $requestBody)
    {
        return $this->sendSynchronousApiRequest($httpMethod, $apiUri, $requestBody, static::MIME_TYPE_PLAIN_TEXT);
    }

    /**
     * @param string $httpMethod
     * @param string $apiUri
     * @param string $requestBody
     * @return mixed
     */
    public function sendSynchronousJsonApiRequest($httpMethod, $apiUri, $requestBody) {
        return $this->sendSynchronousApiRequest($httpMethod, $apiUri, $requestBody, static::MIME_TYPE_JSON);
    }

    /**
     * @param string $httpMethod
     * @param string $apiUri
     * @param string $requestBody
     * @param string $bodyContentType
     * @return mixed
     * @throws TransportException
     */
    public function sendSynchronousApiRequest(
        $httpMethod,
        $apiUri,
        $requestBody,
        $bodyContentType
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
                        'Content-Type' => $bodyContentType,
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
     * @param HttpClientException $exception
     * @throws TransportException
     */
    protected function handleClientException(HttpClientException $exception)
    {
        switch ($exception->getResponse()->getStatusCode()) {
            case 401:
                throw new UnauthorizedException("Unauthorized API request.");
            default:
                throw new ClientException($exception->getMessage());
        }
    }
}
