<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Exceptions\DTOs\Payments;

use AsaasPhpSdk\Exceptions\DTOs\Base\InvalidDataException;

/**
 * Represents an error for invalid data provided for a payment.
 *
 * This exception is thrown during the validation of payment-related DTOs
 * (e.g., `CreatePaymentDTO`) to indicate that the provided data is invalid,
 * such as a missing required field or a malformed value. It uses static
 * factory methods for creating consistent error messages.
 */
final class InvalidPaymentDataException extends InvalidDataException {}
