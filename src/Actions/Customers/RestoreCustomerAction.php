<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Customers;

use AsaasPhpSdk\Actions\Base\RestoreByIdAction;

/**
 * Restores a previously deleted customer.
 *
 * @see https://docs.asaas.com/reference/restaurar-cliente-removido Official Asaas API Documentation
 *  
 */
final class RestoreCustomerAction extends RestoreByIdAction
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
        return 'customers/' . rawurlencode($id) . '/restore';
    }
}
