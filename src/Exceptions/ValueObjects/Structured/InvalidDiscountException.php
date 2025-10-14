<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Exceptions\ValueObjects\Structured;

use AsaasPhpSdk\Exceptions\ValueObjects\InvalidValueObjectException;

/**
 * Represents an error for an invalid Discount configuration.
 *
 * This exception is typically thrown by the `Discount` Value Object when the
 * provided data is invalid, such as a negative value or an unknown
 * discount type.
 */
class InvalidDiscountException extends InvalidValueObjectException {}
