<?php
namespace SergioCarlos\IBMWatson\WatsonLanguageTranslator\Exception;

/**
 * This is usually thrown when the API client cannot authorize against the API.
 * Verify the credentials being supplied, and that the server is allowed to contact the API endpoint.
 */
class Unauthorized extends Transport
{

}
