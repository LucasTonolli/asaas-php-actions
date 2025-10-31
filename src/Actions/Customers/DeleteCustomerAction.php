<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Customers;

use AsaasPhpSdk\Actions\Base\DeleteByIdAction;

/**
 * Deletes an existing customer by their ID.
 *
 * @see https://docs.asaas.com/reference/remover-cliente Official Asaas API Documentation
 */
final class DeleteCustomerAction extends DeleteByIdAction
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
