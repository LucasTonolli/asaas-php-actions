<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Exceptions\ValueObjects\Structured;

use AsaasPhpSdk\Exceptions\ValueObjects\InvalidValueObjectException;

/**
 * Represents an error for an invalid payment Split configuration.
 *
 * This exception is typically thrown by the `Split` Value Object, which manages
 * the entire collection of payment recipients. An error can occur if the split
 * rules are violated, such as the sum of the split amounts not matching the
 * total transaction value.
 */
class InvalidSplitException extends InvalidValueObjectException {}
