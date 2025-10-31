<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Customers;

use AsaasPhpSdk\Actions\Base\GetByIdAction;

/**
 * Retrieves a single customer by their ID.
 *
 * @see https://docs.asaas.com/reference/recuperar-um-unico-cliente Official Asaas API Documentation
 */
final class GetCustomerAction extends GetByIdAction
{
    /**
     * {@inheritDoc}
     */
    protected function getResourceName(): string
    {
        return 'Customer';
    }

    /**
     * {@inheritDoc}
     */
    protected function getEndpoint(string $id): string
    {
        return 'customers/'.rawurlencode($id);
    }
}
