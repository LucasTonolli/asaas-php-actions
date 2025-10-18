<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Payments;

use AsaasPhpSdk\Actions\Base\AbstractAction;
use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\RateLimitException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

final class GetPaymentTicketLineAction extends AbstractAction
{
    /**
     * Retrieves the ticket line information for a specific payment.
     *
     * @param  string  $paymentId  The ID of the payment.
     * @return array <string, mixed> The ticket line information.
     *
     * @throws AuthenticationException If authentication fails.
     * @throws NotFoundException If the payment is not found.
     * @throws RateLimitException If the rate limit is exceeded.
     * @throws ValidationException If the request data is invalid.
     * @throws ApiException For other API-related errors.
     */
    public function handle(string $paymentId): array
    {
        $normalizedPaymentId = trim($paymentId);
        if (empty($normalizedPaymentId)) {
            throw new \InvalidArgumentException('Payment ID cannot be empty');
        }

        return $this->executeRequest(
            fn () => $this->client->get('payments/'.rawurlencode($normalizedPaymentId).'/identificationField')
        );
    }
}
