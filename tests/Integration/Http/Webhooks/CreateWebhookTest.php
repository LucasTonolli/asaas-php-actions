<?php

use AsaasPhpSdk\DTOs\Webhooks\Enums\EventEnum;
use AsaasPhpSdk\DTOs\Webhooks\Enums\SendTypeEnum;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

describe('Create Webhook', function (): void {
    beforeEach(function (): void {
        $config = sandboxConfig();
        $this->asaasClient = new AsaasPhpSdk\AsaasClient($config);
    });

    it('creates a webhook successfully', function (): void {
        $id = uniqid();
        $response = $this->asaasClient->webhook()->create([
            'url' => 'https://example.com/webhook-'.$id,
            'name' => 'Test Webhook - '.$id,
            'email' => 'iEo0Q@example.com',
            'sendType' => SendTypeEnum::Sequentially->value,
            'events' => [
                EventEnum::PaymentCreated->value,
                EventEnum::PaymentDeleted->value,
            ],
        ]);
        expect($response['id'])->not()->toBeEmpty()
            ->and($response['url'])->toBe('https://example.com/webhook-'.$id)
            ->and($response['events'])->toHaveLength(2);
    });

    it('throws an exception if url is invalid', function (): void {
        $this->asaasClient->webhook()->create([
            'url' => 'invalid-url',
            'name' => 'Test Webhook',
            'email' => 'iEo0Q@example.com',
            'sendType' => SendTypeEnum::Sequentially->value,
            'events' => [
                EventEnum::PaymentCreated->value,
                EventEnum::PaymentDeleted->value,
            ],
        ]);
    })->throws(ValidationException::class, 'Invalid URL');

    it('matches the expected response structure', function (): void {
        $id = uniqid();
        $response = $this->asaasClient->webhook()->create([
            'url' => 'https://example.com/webhook-'.$id,
            'name' => 'Test Webhook - '.$id,
            'email' => 'iEo0Q@example.com',
            'sendType' => SendTypeEnum::Sequentially->value,
            'events' => [
                EventEnum::PaymentCreated->value,
                EventEnum::PaymentDeleted->value,
            ],
        ]);
        expect($response)->toHaveKeys([
            'id',
            'name',
            'url',
            'email',
            'enabled',
            'interrupted',
            'apiVersion',
            'sendType',
            'events',
        ]);
    });
});
