<?php
namespace SergioCarlos\IBMWatson\WatsonLanguageTranslator\Translator;

interface ServiceInterface
{
    /**
     * Which language is $text?
     * Returns the 2 language code identifier.
     * @param string $text
     * @return string
     */
    public function identifyLanguage($text);

    /**
     * Simply specify the text and target language code, get a translation back.
     * @param string $text
     * @param string $targetLanguageCode
     * @return string
     */
    public function simpleTranslate($text, $targetLanguageCode);
}
