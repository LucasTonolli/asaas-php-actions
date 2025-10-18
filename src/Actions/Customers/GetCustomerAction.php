<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Customers;

use AsaasPhpSdk\Actions\Base\AbstractAction;
use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\RateLimitException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

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
     * @param  string  $customerId  The unique identifier of the customer to be retrieved.
     * @return array<string, mixed> An array containing the data of the specified customer.
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
            fn () => $this->client->get('customers/'.rawurlencode($normalizedId))
        );
    }
}
