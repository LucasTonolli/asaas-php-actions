<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Exceptions\ValueObjects\Structured;

use AsaasPhpSdk\Exceptions\ValueObjects\InvalidValueObjectException;

/**
 * Represents an error for an invalid Fine (multa) configuration.
 *
 * This exception is typically thrown by the `Fine` Value Object when the
 * provided data is invalid, such as a negative value or an unknown
 * fine type.
 */
class InvalidFineException extends InvalidValueObjectException {}
