<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Services;

use AsaasPhpSdk\Actions\Webhooks\CreateWebhookAction;
use AsaasPhpSdk\Actions\Webhooks\DeleteWebhookAction;
use AsaasPhpSdk\Actions\Webhooks\GetWebhookAction;
use AsaasPhpSdk\DTOs\Webhooks\CreateWebhookDTO;
use AsaasPhpSdk\Services\Base\AbstractService;

/**
 * Provides a user-friendly interface for all webhooks-related operations.
 *
 * This service acts as the main entry point for managing webhooks in the Asaas API.
 * It abstracts the underlying complexity of DTOs and Actions, providing a clean
 * and simple API for the SDK consumer.
 */
final class WebhookService extends AbstractService
{
    /**
     * Creates a new webhook.
     *
     * @see https://docs.asaas.com/reference/criar-novo-webhook
     *
     * @param  array<string, mixed>  $data  webhook data.
     * @return array<string, mixed> An array representing the newly created webhook as returned by the API.
     */
    public function create(array $data): array
    {
        $dto = $this->createDTO(CreateWebhookDTO::class, $data);
        $action = new CreateWebhookAction($this->client, $this->responseHandler);

        return $action->handle($dto);
    }

    /**
     * Retrieves a webhook by its ID.
     *
     * @see https://docs.asaas.com/reference/recuperar-um-unico-webhook
     *
     * @param  string  $id  The ID of the webhook to retrieve.
     * @return array<string, mixed> An array representing the retrieved webhook as returned by the API.
     */
    public function get(string $id): array
    {
        $action = new GetWebhookAction($this->client, $this->responseHandler);

        return $action->handle($id);
    }

    /**
     * Deletes a webhook by its ID.
     *
     * @see https://docs.asaas.com/reference/excluir-um-webhook
     *
     * @param  string  $id  The ID of the webhook to delete.
     * @return array<string, mixed> An array confirming the deletion.
     */
    public function delete(string $id): array
    {
        $action = new DeleteWebhookAction($this->client, $this->responseHandler);

        return $action->handle($id);
    }
}
