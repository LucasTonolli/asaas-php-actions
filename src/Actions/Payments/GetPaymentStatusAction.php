<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Payments;

use AsaasPhpSdk\Actions\Base\AbstractAction;
use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\RateLimitException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

final class GetPaymentStatusAction extends AbstractAction
{
    /**
     * Retrieves the status of a payment by its ID.
     *
     * This action performs a pre-request validation to ensure the ID is not
     * empty and then sends a GET request to the 'payments/{id}/status' endpoint.
     *
     * @see https://docs.asaas.com/reference/recuperar-status-de-uma-cobranca Official Asaas API Documentation
     *
     * @param  string  $paymentId  The unique identifier of the payment whose status is to be retrieved.
     * @return array<string, mixed> An array containing the status information of the specified payment.
     *
     * @throws \InvalidArgumentException if the provided payment ID is empty.
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws ValidationException
     * @throws RateLimitException
     * @throws ApiException
     */
    public function handle(string $paymentId): array
    {
        $normalizedId = trim($paymentId);
        if ($normalizedId === '') {
            throw new \InvalidArgumentException('Payment ID cannot be empty');
        }

        return $this->executeRequest(
            fn() => $this->client->get('payments/' . rawurlencode($normalizedId) . '/status')
        );
    }
}
