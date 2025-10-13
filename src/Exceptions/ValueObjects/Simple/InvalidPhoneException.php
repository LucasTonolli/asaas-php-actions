<?php

namespace AsaasPhpSdk\Exceptions\ValueObjects\Simple;

use AsaasPhpSdk\Exceptions\ValueObjects\InvalidValueObjectException;

/**
 * Represents an error for an invalid phone number format.
 *
 * This exception is typically thrown by the `Phone` Value Object when a given
 * string does not conform to a valid phone number format (e.g., incorrect
 * number of digits).
 */
class InvalidPhoneException extends InvalidValueObjectException {}
