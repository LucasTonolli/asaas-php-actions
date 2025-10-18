<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Customers;

use AsaasPhpSdk\Actions\Base\AbstractAction;
use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\RateLimitException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

final class DeleteCustomerAction extends AbstractAction
{
    /**
     * Deletes an existing customer by their ID.
     *
     * This action performs a pre-request validation to ensure the ID is not
     * empty and then sends a DELETE request to the 'customers/{id}' endpoint.
     *
     * @see https://docs.asaas.com/reference/remover-cliente Official Asaas API Documentation
     *
     * @param  string  $customerId  The unique identifier of the customer to be deleted.
     * @return array An array confirming the deletion, typically containing a 'deleted' flag.
     *
     * @throws \InvalidArgumentException if the provided customer ID is empty.
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws ValidationException
     * @throws RateLimitException
     * @throws ApiException
     */
    public function handle(string $customerId): array
    {
        $normalizedId = trim($customerId);
        if ($normalizedId === '') {
            throw new \InvalidArgumentException('Customer ID cannot be empty');
        }

        return $this->executeRequest(
            fn () => $this->client->delete('customers/'.rawurlencode($normalizedId))
        );
    }
}
