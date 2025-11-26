<?php

use AsaasPhpSdk\Actions\Webhooks\GetWebhookAction;
use AsaasPhpSdk\DTOs\Webhooks\Enums\EventEnum;
use AsaasPhpSdk\DTOs\Webhooks\Enums\SendTypeEnum;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;
use AsaasPhpSdk\Support\Helpers\ResponseHandler;


describe('GetWebhookAction', function (): void {
    it('gets a webhook by id', function (): void {
        // Arrange
        $webhookId = 'wh_123456';
        $client = mockClient([
            mockResponse([
                'id' => $webhookId,
                'name' => 'My Webhook',
                'url' => 'https://example.com/webhook',
                'enabled' => true,
                'interrupted' => false,
                'apiVersion' => 3,
                'sendType' => SendTypeEnum::Sequentially->value,
                'events' => [
                    EventEnum::PaymentReceived->value,
                    EventEnum::PaymentAnticipated->value
                ]
            ], 200)
        ]);

        // Act
        $action = new GetWebhookAction($client, new ResponseHandler);
        $result = $action->handle($webhookId);

        // Assert
        expect($result)->toBeArray()
            ->and($result['id'])->toBe($webhookId)
            ->and($result['name'])->toBe('My Webhook');
    });

    it('throws NotFoundException on 404 status code', function (): void {
        // Arrange
        $webhookId = 'wh_notfound';
        $client = mockClient([
            mockErrorResponse('Webhook not found', 404),
        ]);
        $action = new GetWebhookAction($client, new ResponseHandler);
        $action->handle($webhookId);
    })->throws(NotFoundException::class, 'Resource not found');


    it('throws ValidationException on 400 status code', function (): void {
        // Arrange
        $webhookId = 'invalid-id';
        $asaasClient = mockClient([
            mockErrorResponse('Invalid request.', 400, [
                ['description' => 'ID format is invalid']
            ]),
        ]);

        // Act
        $action = new GetWebhookAction($asaasClient, new ResponseHandler);
        $action->handle($webhookId);
    })->throws(ValidationException::class, 'ID format is invalid');
});
