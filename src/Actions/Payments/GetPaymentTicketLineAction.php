<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Payments;

use AsaasPhpSdk\Actions\Base\GetByIdAction;

/**
 * Retrieves the ticket line information for a specific payment.
 *
 * @see https://docs.asaas.com/reference/obter-linha-digitavel-do-boleto Official Asaas API Documentation
 */
final class GetPaymentTicketLineAction extends GetByIdAction
{
    /**
     * {@inheritDoc}
     */
    protected function getResourceName(): string
    {
        return 'Payment';
    }

    /**
     * {@inheritDoc}
     */
    protected function getEndpoint(string $id): string
    {
        return "payments/" . rawurlencode($id) . "/identificationField";
    }
}
