<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Payments;

use AsaasPhpSdk\Actions\Base\AbstractAction;
use AsaasPhpSdk\Actions\Traits\ValidateResourceIdTrait;
use AsaasPhpSdk\DTOs\Payments\UpdatePaymentDTO;
use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\RateLimitException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

final class UpdatePaymentAction extends AbstractAction
{
    use ValidateResourceIdTrait;

    /**
     * Updates an existing payment by its ID.
     *
     * @see https://docs.asaas.com/reference/atualizar-cobranca-existente Official Asaas API Documentation
     *
     * @param  string  $paymentId  The unique identifier of the payment to be updated.
     * @param  UpdatePaymentDTO  $data  A DTO containing the payment data to be updated.
     * @return array<string, mixed> An array containing the full, updated data of the payment.
     *
     * @throws \InvalidArgumentException if the provided payment ID is empty.
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws ValidationException
     * @throws RateLimitException
     * @throws ApiException
     */
    public function handle(string $paymentId, UpdatePaymentDTO $data): array
    {
        $normalizedId = $this->validateAndNormalizeId($paymentId, 'Payment');

        return $this->executeRequest(
            fn () => $this->client->put('payments/'.rawurlencode($normalizedId), ['json' => $data->toArray()])
        );
    }
}
