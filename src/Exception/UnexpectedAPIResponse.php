<?php
namespace SergioCarlos\IBMWatson\WatsonLanguageTranslator\Exception;

/**
 * This is thrown when we get a proper API response, but data is not what we expect.
 * This usually indicates a problem with the library, and as such, it should be extremely
 * rare.
 */
class UnexpectedAPIResponse extends \Exception
{

}
