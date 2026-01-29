<?php

namespace AsaasPhpSdk\Actions\Base;

use AsaasPhpSdk\Support\Http\HttpTransporter;

/**
 * Base class for all SDK Actions.
 *
 * This class serves as a template for API interactions, providing concrete 
 * actions with access to the internal HttpTransporter. It ensures a 
 * consistent dependency injection pattern across all SDK use cases.
 *
 * @internal This class is a core architectural component and should not be 
 * instantiated or extended by SDK consumers.
 */
abstract class AbstractAction
{
    /**
     * AbstractAction constructor.
     *
     * @param HttpTransporter $transporter The internal engine used to send 
     * PSR-compliant requests and handle responses.
     */
    public function __construct(
        protected readonly HttpTransporter $transporter
    ) {}
}
