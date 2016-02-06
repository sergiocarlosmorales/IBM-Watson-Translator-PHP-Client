<?php
/*
 * Make sure you have already installed dependencies via composer,
 * otherwise the require will fail.
 */
require __DIR__ . '../../../vendor/autoload.php';
use SergioCarlos\IBMWatson\WatsonLanguageTranslator\Translator\Factory as TranslatorFactory;

$username = 'your-username-for-the-translator-service-that-you-get-from-BlueMix';
$password = 'your-password';

$translator = TranslatorFactory::getTranslator($username, $password);
$textToTranslate = 'pomme'; // This is 'apple' in French.
$targetLanguageCode = 'en'; // 'en' is English, translate to English.

echo $translator->simpleTranslate($textToTranslate, $targetLanguageCode);