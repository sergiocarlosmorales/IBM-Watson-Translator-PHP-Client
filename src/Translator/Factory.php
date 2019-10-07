<?php
namespace SergioCarlos\IBMWatson\WatsonLanguageTranslator\Translator;

use GuzzleHttp\Client as HttpClient;
use SergioCarlos\IBMWatson\WatsonLanguageTranslator\Api\Transport as ApiTransport;
use SergioCarlos\IBMWatson\WatsonLanguageTranslator\Translator\Client as TranslatorClient;

class Factory
{
    /**
     * @param string $username
     * @param string $password
     * @param string $host
     * @return Client
     */
    public static function getTranslator($username, $password, $host = null)
    {
	if ($host) {
	    $httpClient = new HttpClient([
                'base_uri' => $host,
            ]);
	}
        $apiTransport = new ApiTransport($username, $password, $httpClient);
        $translatorClient = new TranslatorClient($apiTransport);
        return $translatorClient;
    }
}
