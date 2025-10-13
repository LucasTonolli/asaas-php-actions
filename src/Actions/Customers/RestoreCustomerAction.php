<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Customers;

use AsaasPhpSdk\Actions\Base\AbstractAction;

final class RestoreCustomerAction extends AbstractAction
{
    /**
     * Restores a previously deleted customer.
     *
     * This action performs a pre-request validation to ensure the ID is not
     * empty and then sends a POST request to the 'customers/{id}/restore' endpoint.
     *
     * @see https://docs.asaas.com/reference/restaurar-cliente-removido Official Asaas API Documentation
     *
     * @param  string  $id  The unique identifier of the customer to be restored.
     * @return array An array containing the data of the restored customer.
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
            fn() => $this->client->post('customers/' . rawurlencode($normalizedId) . '/restore')
        );
    }
}
