<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Customers;

use AsaasPhpSdk\Actions\AbstractAction;

final class GetCustomerAction extends AbstractAction
{
    /**
     * Retrieves a single customer by their ID.
     *
     * This action performs a pre-request validation to ensure the ID is not
     * empty and then sends a GET request to the 'customers/{id}' endpoint.
     *
     * @see https://docs.asaas.com/reference/recuperar-um-unico-cliente Official Asaas API Documentation
     *
     * @param  string  $id  The unique identifier of the customer to be retrieved.
     * @return array An array containing the data of the specified customer.
     *
     * @throws \InvalidArgumentException if the provided customer ID is empty.
     * @throws \AsaasPhpSdk\Exceptions\Api\ApiException
     * @throws \AsaasPhpSdk\Exceptions\Api\AuthenticationException
     * @throws \AsaasPhpSdk\Exceptions\Api\NotFoundException if the customer with the given ID does not exist.
     */
    public function handle(string $id): array
    {
        $normalizedId = trim($id);
        if ($normalizedId === '') {
            throw new \InvalidArgumentException('Customer ID cannot be empty');
        }

        return $this->executeRequest(
            fn() => $this->client->get('customers/' . rawurlencode($normalizedId))
        );
    }
}
