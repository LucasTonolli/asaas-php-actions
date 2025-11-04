<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Exceptions\DTOs\Webhook;

use AsaasPhpSdk\Exceptions\DTOs\Base\InvalidDataException;

/**
 * Represents an error for invalid data provided for a webhook.
 *
 * This exception is thrown during the validation of webhook-related DTOs
 * (e.g., `CreateWebhookDTO`) to indicate that the provided data is invalid,
 * such as a missing required field or a malformed value. It uses static
 * factory methods for creating consistent error messages.
 */
class InvalidWebhookDataException extends InvalidDataException {}
