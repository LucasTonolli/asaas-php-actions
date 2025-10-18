<?php

namespace AsaasPhpSdk\Actions\Customers;

use AsaasPhpSdk\Actions\Base\AbstractAction;
use AsaasPhpSdk\DTOs\Customers\ListCustomersDTO;
use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\RateLimitException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

final class ListCustomersAction extends AbstractAction
{
    /**
     * Retrieves a paginated list of customers, with optional filters.
     *
     * This action sends a GET request to the 'customers' endpoint. All filtering
     * and pagination parameters are encapsulated in the ListCustomersDTO.
     *
     * @see https://docs.asaas.com/reference/listar-clientes Official Asaas API Documentation
     *
     * @param  ListCustomersDTO  $data  A DTO containing filter and pagination parameters (e.g., name, email, limit, offset).
     * @return array <string, mixed> A paginated list of customers. The structure includes pagination info and a 'data' key with the customers array.
     *
     * @throws ApiException
     * @throws ValidationException Can be thrown if an invalid filter is sent.
     * @throws AuthenticationException
     * @throws RateLimitException
     */
    public function handle(ListCustomersDTO $data): array
    {
        return $this->executeRequest(
            fn() => $this->client->get('customers', ['query' => $data->toArray()])
        );
    }
}
