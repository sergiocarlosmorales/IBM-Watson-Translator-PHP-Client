<?php
namespace SergioCarlos\IBMWatson\WatsonLanguageTranslator\Translator;

use SergioCarlos\IBMWatson\WatsonLanguageTranslator\Api\Transport as ApiTransport;
use SergioCarlos\IBMWatson\WatsonLanguageTranslator\Exception\UnexpectedAPIResponse;

/**
 * This client encapsulates the translation domain (business logic).
 */
class Client implements ServiceInterface
{
    /**
     * @var ApiTransport
     */
    protected $apiTransport;

    public function __construct(ApiTransport $apiTransport)
    {
        $this->apiTransport = $apiTransport;
    }

    /**
     * Returns the 2 char language code.
     * @param string $text
     * @return string
     */
    public function identifyLanguage($text)
    {
        $response = $this->sendApiRequest('POST', 'identify', $text);
        if (empty($response->languages)) {
            $this->handleUnexpectedApiResponse('language identification');
        }

        return $response->languages[0]->language;
    }

    /**
     * Interact with our bare API client to send a request.
     * @param string $httpMethod
     * @param string $apiUri
     * @param string $requestBody
     * @param bool $jsonRequestBodyFlag
     * @return mixed
     */
    protected function sendApiRequest(
        $httpMethod,
        $apiUri,
        $requestBody,
        $jsonRequestBodyFlag = false
    ) {
        if ($jsonRequestBodyFlag) {
            return $this->apiTransport->sendSynchronousJsonApiRequest(
                $httpMethod,
                $apiUri,
                $requestBody
            );
        } else {
            return $this->apiTransport->sendSynchronousPlainTextApiRequest(
                $httpMethod,
                $apiUri,
                $requestBody
            );
        }

    }

    /**
     * Simple translation, specify text and target language.
     * This makes first a call to identify the source language.
     * @param string $text
     * @param string $targetLanguageCode 2 character language code.
     * @return string
     */
    public function simpleTranslate($text, $targetLanguageCode)
    {
        $translateRequest = new \stdClass();
        $translateRequest->source = $this->identifyLanguage($text);
        $translateRequest->target = $targetLanguageCode;
        $translateRequest->text = [$text];

        $response = $this->sendApiRequest(
            'POST',
            'translate',
            json_encode($translateRequest),
            true
        );
        if (empty($response->translations)) {
            $this->handleUnexpectedApiResponse('translation');
        }

        return $response->translations[0]->translation;
    }

    /**
     * This gets called when the API response was unexpected.
     * @param string $failedActivity
     * @throws UnexpectedAPIResponse
     */
    protected function handleUnexpectedApiResponse($failedActivity)
    {
        throw new UnexpectedAPIResponse("Unexpected API response for {$failedActivity}.");
    }
}
