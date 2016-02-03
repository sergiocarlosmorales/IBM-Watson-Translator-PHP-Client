<?php
namespace SergioCarlos\IBMWatson\WatsonLanguageTranslator\Translator\Tests;

use SergioCarlos\IBMWatson\WatsonLanguageTranslator\Translator\Factory;
use SergioCarlos\IBMWatson\WatsonLanguageTranslator\Translator\Client as TranslatorClient;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactoryReturnsTranslator()
    {
        $this->assertInstanceOf(
            TranslatorClient::class,
            Factory::getTranslator('username', 'password')
        );
    }
}