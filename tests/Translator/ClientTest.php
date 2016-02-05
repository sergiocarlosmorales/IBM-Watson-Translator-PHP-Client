<?php
namespace SergioCarlos\IBMWatson\WatsonLanguageTranslator\Translator\Tests;

use SergioCarlos\IBMWatson\WatsonLanguageTranslator\Api\Transport;
use SergioCarlos\IBMWatson\WatsonLanguageTranslator\Translator\Client;
use SergioCarlos\IBMWatson\WatsonLanguageTranslator\Exception\UnexpectedAPIResponse;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testIdentifyLanguageSendsExpectedAPIRequest()
    {
        $textToIdentify = 'hello';
        $client = $this->getMockClient(['sendApiRequest']);

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
        $client = $this->getMockClient(['sendApiRequest']);
        $client->expects($this->once())
            ->method('sendApiRequest')
            ->will($this->returnValue($stubResponse));
        $this->assertEquals(
            $stubResponse->languages[0]->language,
            $client->identifyLanguage($textToIdentify)
        );
    }

    public function testIdentifyLanguageThrowsExceptionWhenNoLanguagesReturned()
    {
        $apiResponse = $this->getStubLanguageIdentificationResponse();
        unset($apiResponse->languages);
        $client = $this->getMockClient(['sendApiRequest']);
        $client->expects($this->once())
            ->method('sendApiRequest')
            ->will($this->returnValue($apiResponse));
        $this->setExpectedException(UnexpectedAPIResponse::class, 'language identification');
        $client->identifyLanguage('something');
    }

    public function testSimpleTranslateTriggersLanguageIdentification()
    {
        $client = $this->getMockClient([
            'identifyLanguage',
            'sendApiRequest'
        ]);
        $client->expects($this->once())
            ->method('identifyLanguage');
        $client->expects($this->once())
            ->method('sendApiRequest')
            ->will($this->returnValue($this->getStubTranslationResponse('whatever')));
        $client->simpleTranslate('yolo', 'zx');
    }

    public function testSimpleTranslateReturnsTextFromResponse()
    {
        $client = $this->getMockClient([
            'identifyLanguage',
            'sendApiRequest'
        ]);
        $translatedText = 'hello';
        $client->expects($this->once())
            ->method('identifyLanguage');
        $client->expects($this->once())
            ->method('sendApiRequest')
            ->will($this->returnValue($this->getStubTranslationResponse($translatedText)));
        $result = $client->simpleTranslate('yolo', 'zx');
        $this->assertEquals($translatedText, $result);
    }

    public function testSimpleTranslateThrowsExceptionIfNoTranslationsReturned()
    {
        $client = $this->getMockClient([
            'identifyLanguage',
            'sendApiRequest'
        ]);
        $response = $this->getStubTranslationResponse('hola');
        unset($response->translations);
        $client->expects($this->any())
            ->method('identifyLanguage');
        $client->expects($this->once())
            ->method('sendApiRequest')
            ->will($this->returnValue($response));
        $this->setExpectedException(UnexpectedAPIResponse::class, 'translation');
        $client->simpleTranslate('yolo', 'zx');
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
     * @param string $translationResult
     * @return \stdClass
     */
    protected function getStubTranslationResponse($translationResult)
    {
        $translationResponse = new \stdClass();
        $translationResponse->translation = $translationResult;
        $response = new \stdClass();
        $response->translations = [
            0 => $translationResponse
        ];

        return $response;
    }

    /**
     * @param array $methodsToMock
     * @return \PHPUnit_Framework_MockObject_MockObject|Client
     */
    protected function getMockClient($methodsToMock)
    {
        return $this->getMock(
            Client::class,
            $methodsToMock,
            [
                new Transport('username', 'password')
            ]
        );
    }
}