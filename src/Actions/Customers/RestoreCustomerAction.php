<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Customers;

use AsaasPhpSdk\Actions\Base\AbstractAction;
use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\RateLimitException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

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
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws ValidationException
     * @throws RateLimitException
     * @throws ApiException
     */
    public function handle(string $id): array
    {
        $normalizedId = trim($id);
        if ($normalizedId === '') {
            throw new \InvalidArgumentException('Customer ID cannot be empty');
        }

        return $this->executeRequest(
            fn () => $this->client->post('customers/'.rawurlencode($normalizedId).'/restore')
        );
    }
}
