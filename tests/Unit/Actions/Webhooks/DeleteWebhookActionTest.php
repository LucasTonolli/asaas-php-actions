<?php

use AsaasPhpSdk\Actions\Webhooks\DeleteWebhookAction;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Support\Helpers\ResponseHandler;

describe('DeleteWebhookAction', function (): void {
    it('deletes a webhook successfully (200)', function (): void {
        // Arrange
        $webhookId = 'wh_123456';
        $client = mockClient([
            mockResponse([
                'id' => $webhookId,
                'deleted' => true,
                'object' => 'webhook',
            ], 200)
        ]);
        $action = new DeleteWebhookAction($client, new ResponseHandler);

        // Act
        $result = $action->handle($webhookId);

        // Assert
        expect($result)->toBeArray()
            ->and($result['id'])->toBe($webhookId)
            ->and($result['deleted'])->toBeTrue();
    });

    it('throws NotFoundException on 404 error', function (): void {
        // Arrange
        $webhookId = 'wh_notfound';
        $client = mockClient([
            mockErrorResponse('Webhook not found', 404),
        ]);
        $action = new DeleteWebhookAction($client, new ResponseHandler);

        // Act & Assert
        $action->handle($webhookId);
    })->throws(NotFoundException::class, 'Resource not found');

    it('throws InvalidArgumentException when ID is empty', function (): void {
        // Arrange
        $client = mockClient([]);
        $action = new DeleteWebhookAction($client, new ResponseHandler);

        // Act & Assert
        $action->handle('');
    })->throws(InvalidArgumentException::class, 'Webhook ID cannot be empty');
});
