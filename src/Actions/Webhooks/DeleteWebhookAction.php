<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Webhooks;

use AsaasPhpSdk\Actions\Base\DeleteByIdAction;

/**
 * Deletes an existing webhook by its ID.
 *
 * @see https://docs.asaas.com/reference/excluir-um-webhook Official Asaas API Documentation
 */
final class DeleteWebhookAction extends DeleteByIdAction
{
    /**
     * {@inheritDoc}
     */
    protected function getResourceName(): string
    {
        return 'Webhook';
    }

    /**
     * {@inheritDoc}
     */
    protected function getEndpoint(string $id): string
    {
        return 'webhooks/'.rawurlencode($id);
    }
}
