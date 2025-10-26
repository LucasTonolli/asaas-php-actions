<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Exceptions\DTOs\CreditCard;

use AsaasPhpSdk\Exceptions\AsaasException;

/**
 * Represents an error for invalid data provided for a credit card.
 *
 * This exception is thrown during the validation of credit card-related DTOs
 * (e.g., `CreateCreditCardDTO`) to indicate that the provided data is invalid,
 * such as a missing required field or a malformed value. It uses static
 * factory methods for creating consistent error messages.
 */
class InvalidCreditCardDataException extends AsaasException {}
