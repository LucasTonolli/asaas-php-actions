<?php

use AsaasPhpSdk\Actions\Webhooks\CreateWebhookAction;
use AsaasPhpSdk\DTOs\Webhooks\CreateWebhookDTO;
use AsaasPhpSdk\DTOs\Webhooks\Enums\EventEnum;
use AsaasPhpSdk\DTOs\Webhooks\Enums\SendTypeEnum;
use AsaasPhpSdk\Exceptions\Api\ValidationException;
use AsaasPhpSdk\Support\Helpers\ResponseHandler;

describe('Create Webhook Action', function (): void {
    it('creates a webhook successfully', function (): void {
        $client = mockClient([
            mockResponse([
                'id' => 'wh_123',
                'name' => 'Test Webhook',
                'url' => 'https://example.com/webhook',
                'email' => 'ieo0q@example.com',
                'enabled' => true,
                'interrupted' => false,
                'apiVersion' => 3,
                'hasAuthToken' => false,
                'sendType' => SendTypeEnum::Sequentially->value,
                'events' => [
                    EventEnum::PaymentReceived->value,
                    EventEnum::PaymentConfirmed->value,
                ],
            ], 200),
        ]);

        $action = new CreateWebhookAction($client, new ResponseHandler);
        $dto = CreateWebhookDTO::fromArray([
            'name' => 'Test Webhook',
            'url' => 'https://example.com/webhook',
            'email' => 'iEo0Q@example.com',
            'enabled' => true,
            'interrupted' => false,
            'apiVersion' => 3,
            'authToken' => 'test-token',
            'sendType' => SendTypeEnum::Sequentially->value,
            'events' => [
                EventEnum::PaymentReceived->value,
                EventEnum::PaymentConfirmed->value,
            ],
        ]);

        $result = $action->handle($dto);

        expect($result)->toBeArray()
            ->and($result['id'])->toBe('wh_123')
            ->and($result['name'])->toBe('Test Webhook')
            ->and($result['url'])->toBe('https://example.com/webhook')
            ->and($result['email'])->toBe('ieo0q@example.com')
            ->and($result['enabled'])->toBe(true)
            ->and($result['interrupted'])->toBe(false)
            ->and($result['apiVersion'])->toBe(3)
            ->and($result['hasAuthToken'])->toBe(false)
            ->and($result['sendType'])->toBe(SendTypeEnum::Sequentially->value)
            ->and($result['events'])->toBe([
                EventEnum::PaymentReceived->value,
                EventEnum::PaymentConfirmed->value,
            ]);
    });

    it('throws ValidationException on 400 error', function (): void {
        $client = mockClient([
            mockErrorResponse('Invalid webhook data', 400, [
                ['description' => 'Invalid URL'],
            ]),
        ]);

        $action = new CreateWebhookAction($client, new ResponseHandler);
        $dto = CreateWebhookDTO::fromArray([
            'name' => 'Test Webhook',
            'url' => 'https://example.com/webhook',
            'email' => 'iEo0Q@example.com',
            'enabled' => true,
            'interrupted' => false,
            'apiVersion' => 3,
            'authToken' => 'test-token',
            'sendType' => SendTypeEnum::Sequentially->value,
            'events' => [
                EventEnum::PaymentReceived->value,
                EventEnum::PaymentConfirmed->value,
            ],
        ]);

        expect(fn () => $action->handle($dto))->toThrow(ValidationException::class);
    });
});
