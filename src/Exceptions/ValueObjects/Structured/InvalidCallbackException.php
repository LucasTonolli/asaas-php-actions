<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Exceptions\ValueObjects\Structured;

use AsaasPhpSdk\Exceptions\ValueObjects\InvalidValueObjectException;

/**
 * Represents an error for an invalid Callback configuration.
 *
 * This exception is typically thrown by the `Callback` Value Object when
 * the provided data is invalid, such as a malformed URL or incorrect
 * notification settings.
 */
class InvalidCallbackException extends InvalidValueObjectException {}
