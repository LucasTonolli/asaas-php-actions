<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Exceptions\ValueObjects\Structured;

use AsaasPhpSdk\Exceptions\ValueObjects\InvalidValueObjectException;

/**
 * Represents an error for an invalid CreditCard configuration.
 *
 * This exception is typically thrown by the `CreditCard` Value Object when
 * the provided data is invalid, such as missing or incorrect fields.
 */
class InvalidCreditCardException extends InvalidValueObjectException {}
