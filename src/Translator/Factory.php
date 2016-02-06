<?php
namespace SergioCarlos\IBMWatson\WatsonLanguageTranslator\Translator;

use SergioCarlos\IBMWatson\WatsonLanguageTranslator\Api\Transport as ApiTransport;
use SergioCarlos\IBMWatson\WatsonLanguageTranslator\Translator\Client as TranslatorClient;

class Factory
{
    /**
     * @param string $username
     * @param string $password
     * @return Client
     */
    public static function getTranslator($username, $password)
    {
        $apiTransport = new ApiTransport($username, $password);
        $translatorClient = new TranslatorClient($apiTransport);
        return $translatorClient;
    }
}
