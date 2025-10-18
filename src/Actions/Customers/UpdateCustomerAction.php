<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Customers;

use AsaasPhpSdk\Actions\Base\AbstractAction;
use AsaasPhpSdk\DTOs\Customers\UpdateCustomerDTO;
use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\RateLimitException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

final class UpdateCustomerAction extends AbstractAction
{
    /**
     * Updates an existing customer by their ID.
     *
     * This action performs a pre-request validation on the ID and sends a PUT
     * request to the 'customers/{id}' endpoint with the new data.
     *
     * @see https://docs.asaas.com/reference/atualizar-cliente-existente Official Asaas API Documentation
     *
     * @param  string  $customerId  The unique identifier of the customer to be updated.
     * @param  UpdateCustomerDTO  $data  A DTO containing the customer data to be updated.
     * @return array<string, mixed> An array containing the full, updated data of the customer.
     *
     * @throws \InvalidArgumentException if the provided customer ID is empty.
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws ValidationException
     * @throws RateLimitException
     * @throws ApiException
     */
    public function handle(string $customerId, UpdateCustomerDTO $data): array
    {
        $normalizedId = trim($customerId);
        if ($normalizedId === '') {
            throw new \InvalidArgumentException('Customer ID cannot be empty');
        }

        return $this->executeRequest(
            fn () => $this->client->put('customers/'.rawurlencode($normalizedId), ['json' => $data->toArray()])
        );
    }
}
