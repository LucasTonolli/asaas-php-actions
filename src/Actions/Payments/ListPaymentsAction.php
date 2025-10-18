<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Payments;

use AsaasPhpSdk\Actions\Base\AbstractAction;
use AsaasPhpSdk\DTOs\Payments\ListPaymentsDTO;
use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\RateLimitException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

final class ListPaymentsAction extends AbstractAction
{
    /**
     * Retrieves a paginated list of payments, with optional filters.
     *
     * This action sends a GET request to the 'payments' endpoint. All filtering
     * and pagination parameters are encapsulated in the ListPaymentsDTO.
     *
     * @see https://docs.asaas.com/reference/listar-cobrancas Official Asaas API Documentation
     *
     * @param  ListPaymentsDTO  $data  A DTO containing filter and pagination parameters (e.g., installment, billingType, limit, offset).
     * @return array <string, mixed> A paginated list of payments. The structure includes pagination info and a 'data' key with the payments array.
     *
     * @throws ApiException
     * @throws ValidationException Can be thrown if an invalid filter is sent.
     * @throws AuthenticationException
     * @throws RateLimitException
     */
    public function handle(ListPaymentsDTO $data): array
    {
        return $this->executeRequest(
            fn () => $this->client->get('payments', ['query' => $data->toArray()])
        );
    }
}
