<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Payments;

use AsaasPhpSdk\Actions\Base\AbstractAction;
use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\RateLimitException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

final class RestorePaymentAction extends AbstractAction
{
    /**
     * Restores a previously deleted payment.
     *
     * This action performs a pre-request validation to ensure the ID is not
     * empty and then sends a POST request to the 'payments/{id}/restore' endpoint.
     *
     * @see https://docs.asaas.com/reference/restaurar-cobranca-removida Official Asaas API Documentation
     *
     * @param  string  $id  The unique identifier of the payment to be restored.
     * @return array An array containing the data of the restored payment.
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
            fn () => $this->client->post('payments/'.rawurlencode($normalizedId).'/restore')
        );
    }
}
