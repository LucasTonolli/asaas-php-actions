<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Exceptions\ValueObjects\Structured;

use AsaasPhpSdk\Exceptions\ValueObjects\InvalidValueObjectException;

/**
 * Represents an error for an invalid Credit Card Holder Information.
 *
 * This exception is typically thrown by the `CreditCardHolderInfo` Value Object when
 * the provided data is invalid, such as missing required fields or incorrect
 * identification numbers.
 */
class InvalidCreditCardHolderInfoException extends InvalidValueObjectException {}
