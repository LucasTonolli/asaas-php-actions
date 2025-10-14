<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Exceptions\DTOs\Payments;

use AsaasPhpSdk\Exceptions\AsaasException;

/**
 * Represents an error for invalid data provided for a payment.
 *
 * This exception is thrown during the validation of payment-related DTOs
 * (e.g., `CreatePaymentDTO`) to indicate that the provided data is invalid,
 * such as a missing required field or a malformed value. It uses static
 * factory methods for creating consistent error messages.
 */
final class InvalidPaymentDataException extends AsaasException
{
    /**
     * Creates an exception for a missing required field.
     *
     * @param  string  $field  The name of the required field that is missing.
     * @return self A new instance of the exception.
     */
    public static function missingField(string $field): self
    {
        return new self("Required field '{$field}' is missing.", 400);
    }

    /**
     * Creates an exception for a field with an invalid format.
     *
     * @param  string  $field  The name of the field with the invalid format.
     * @param  ?string  $message  An optional, more specific error message. A default is used if not provided.
     * @return self A new instance of the exception.
     */
    public static function invalidFormat(string $field, ?string $message = null): self
    {
        $defaultMessage = "Field '{$field}' has invalid format.";

        return new self($message ?? $defaultMessage, 400);
    }
}
