<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Exceptions\ValueObjects\Structured;

use AsaasPhpSdk\Exceptions\ValueObjects\InvalidValueObjectException;

/**
 * Represents an error for an invalid Split Entry configuration.
 *
 * This exception is typically thrown by the `SplitEntry` Value Object, which
 * represents a single recipient in a payment split. The error can occur if the
 * data is invalid, such as a missing recipient wallet ID or an invalid amount.
 */
class InvalidSplitEntryException extends InvalidValueObjectException {}
