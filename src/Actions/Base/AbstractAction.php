<?php

namespace AsaasPhpSdk\Actions\Base;

use AsaasPhpSdk\Support\Http\HttpTransporter;

/**
 * Base class for all SDK Actions.
 *
 * This class provides a standardized method to execute API requests,
 * centralizing the error handling logic for HTTP exceptions. All concrete
 * action classes (e.g., CreateCustomerAction) should extend this class.
 *
 * @internal This is an internal class and should not be used directly by SDK consumers.
 */
abstract class AbstractAction
{
    /**
     * AbstractAction constructor.
     *
     * @param  HttpTransporter  $transporter          The HTTP client used for making API requests.
     */
    public function __construct(
        protected readonly HttpTransporter $transporter
    ) {}
}
