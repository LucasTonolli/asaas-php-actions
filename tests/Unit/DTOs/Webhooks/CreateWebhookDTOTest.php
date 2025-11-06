<?php

use AsaasPhpSdk\DTOs\Webhooks\CreateWebhookDTO;
use AsaasPhpSdk\DTOs\Webhooks\Enums\EventEnum;
use AsaasPhpSdk\DTOs\Webhooks\Enums\SendTypeEnum;
use AsaasPhpSdk\Exceptions\DTOs\Webhook\InvalidWebhookDataException;
use AsaasPhpSdk\ValueObjects\Simple\Email;

describe('Create Webhook DTO', function (): void {
    it('creates a webhook successfully', function (): void {
        $data = [
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
                EventEnum::PaymentCreated->value,
            ],
        ];

        $dto = CreateWebhookDTO::fromArray($data);
        expect($dto)
            ->toBeInstanceOf(CreateWebhookDTO::class)
            ->name->toBe('Test Webhook')
            ->url->toBe('https://example.com/webhook')
            ->email->toBeInstanceOf(Email::class)
            ->and($dto->email->value())
            ->toBe('ieo0q@example.com')
            ->and($dto->enabled)->toBe(true)
            ->and($dto->interrupted)->toBe(false)
            ->and($dto->apiVersion)->toBe(3)
            ->and($dto->authToken)->toBe('test-token')
            ->and($dto->sendType)->toBe(SendTypeEnum::Sequentially)
            ->and($dto->events)->toBe([
                EventEnum::PaymentReceived,
                EventEnum::PaymentCreated,
            ]);
    });

    it('throws an exception if url is invalid', function (): void {
        $data = [
            'name' => 'Test Webhook',
            'url' => 'invalid-url',
            'email' => 'iEo0Q@example.com',
            'enabled' => true,
            'interrupted' => false,
            'apiVersion' => 3,
            'authToken' => 'test-token',
            'sendType' => SendTypeEnum::Sequentially->value,
            'events' => [
                EventEnum::PaymentReceived->value,
                EventEnum::PaymentCreated->value,
            ],
        ];

        CreateWebhookDTO::fromArray($data);
    })->throws(InvalidWebhookDataException::class, 'Invalid URL');

    it('throws an exception if has an invalid event', function (): void {
        $data = [
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
                'test_event',
            ],
        ];

        CreateWebhookDTO::fromArray($data);
    })->throws(InvalidWebhookDataException::class, 'Invalid event test_event');
});
