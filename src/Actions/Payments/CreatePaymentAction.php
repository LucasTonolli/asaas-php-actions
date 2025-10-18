<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Payments;

use AsaasPhpSdk\Actions\Base\AbstractAction;
use AsaasPhpSdk\DTOs\Payments\CreatePaymentDTO;
use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\RateLimitException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

final class CreatePaymentAction extends AbstractAction
{
    /**
     * Creates a new payment.
     *
     * This action sends a POST request to the 'payments' endpoint. The data is
     * encapsulated and validated by the CreatePaymentDTO before being sent.
     *
     * @see https://docs.asaas.com/reference/criar-nova-cobranca Official Asaas API Documentation
     *
     * @param  CreatePaymentDTO  $data  A Data Transfer Object containing the validated payment data.
     * @return array <string, mixed> An array representing the newly created payment as returned by the API.
     *
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws ValidationException
     * @throws RateLimitException
     * @throws ApiException
     */
    public function handle(CreatePaymentDTO $data): array
    {
        return $this->executeRequest(
            fn () => $this->client->post('payments', ['json' => $data->toArray()])
        );
    }
}
