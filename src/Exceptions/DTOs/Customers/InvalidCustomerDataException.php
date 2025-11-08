<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Exceptions\DTOs\Customers;

use AsaasPhpSdk\Exceptions\DTOs\Base\InvalidDataException;

/**
 * Represents an error for invalid data provided for a customer.
 *
 * This exception is thrown during the validation of customer-related DTOs
 * (e.g., `CreateCustomerDTO`) to indicate that the provided data is invalid,
 * such as a missing required field or a malformed value. It uses static
 * factory methods for creating consistent error messages.
 */
final class InvalidCustomerDataException extends InvalidDataException {}
