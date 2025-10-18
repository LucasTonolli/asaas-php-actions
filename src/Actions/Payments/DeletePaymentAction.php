<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Payments;

use AsaasPhpSdk\Actions\Base\AbstractAction;
use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\RateLimitException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

final class DeletePaymentAction extends AbstractAction
{
    /**
     * Deletes a payment by its ID.
     *
     * This action performs a pre-request validation to ensure the ID is not
     * empty and then sends a DELETE request to the 'payments/{id}' endpoint.
     *
     * @see https://docs.asaas.com/reference/excluir-cobranca Official Asaas API Documentation
     *
     * @param  string  $paymentId  The ID of the payment to delete.
     * @return array <string, mixed> An array confirming the deletion, typically containing a 'deleted' flag.
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
            fn() => $this->client->delete('payments/' . rawurlencode($normalizedId))
        );
    }
}
