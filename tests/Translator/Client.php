<?php
namespace SergioCarlos\IBMWatson\WatsonLanguageTranslator\Translator\Tests;

use SergioCarlos\IBMWatson\WatsonLanguageTranslator\Translator\Client;
use SergioCarlos\IBMWatson\WatsonLanguageTranslator\Api\Transport;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testIdentifyLanguageSendsExpectedAPIRequest()
    {
        $textToIdentify = 'hello';
        $client = $this->getMockClient();

        $client->expects($this->once())
            ->method('sendApiRequest')
            ->with('POST', 'identify', $textToIdentify)
            ->will($this->returnValue($this->getStubLanguageIdentificationResponse()));
        $client->identifyLanguage($textToIdentify);
    }

    public function testIdentifyLanguageReturnsLanguageCodeFromResponse()
    {
        $textToIdentify = 'hello';
        $stubResponse = $this->getStubLanguageIdentificationResponse();
        $client = $this->getMockClient();
        $client->expects($this->once())
            ->method('sendApiRequest')
            ->will($this->returnValue($stubResponse));
        $this->assertEquals(
            $stubResponse->languages[0]->language,
            $client->identifyLanguage($textToIdentify)
        );
    }

    /**
     * @return \stdClass
     */
    protected function getStubLanguageIdentificationResponse() {
        $stubLanguageResponse = new \stdClass();
        $stubLanguageResponse->language = 'xx';

        $stubResponse = new \stdClass();
        $stubResponse->languages = [
            0 => $stubLanguageResponse
        ];

        return $stubResponse;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Client
     */
    protected function getMockClient()
    {
        return $this->getMock(
            Client::class,
            [
                'sendApiRequest'
            ],
            [
                new Transport('username', 'password')
            ]
        );
    }
}