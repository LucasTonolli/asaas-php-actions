<?php

use AsaasPhpSdk\DTOs\Webhooks\Enums\EventEnum;
use AsaasPhpSdk\DTOs\Webhooks\Enums\SendTypeEnum;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;

const WEBHOOK_KEYS = [
    'id',
    'name',
    'url',
    'email',
    'enabled',
    'interrupted',
    'apiVersion',
    'sendType',
    'events',
];

describe('Get Webhook', function (): void {
    beforeEach(function (): void {
        $config = sandboxConfig();
        $this->asaasClient = new AsaasPhpSdk\AsaasClient($config);
    });

    it('retrieves a webhook successfully (200)', function (): void {
        $id = uniqid();
        $createWebhookResponse = $this->asaasClient->webhook()->create([
            'url' => 'https://example.com/webhook-'.$id,
            'name' => 'Test Webhook - '.$id,
            'email' => 'iEo0Q@example.com',
            'sendType' => SendTypeEnum::Sequentially->value,
            'events' => [
                EventEnum::PaymentCreated->value,
                EventEnum::PaymentDeleted->value,
            ],
        ]);

        $response = $this->asaasClient->webhook()->get($createWebhookResponse['id']);
        expect($response)->toBeArray()
            ->and($response['id'])->toBe($createWebhookResponse['id'])
            ->and($response['name'])->toBe($createWebhookResponse['name'])
            ->and($response['url'])->toBe($createWebhookResponse['url'])
            ->and($response['enabled'])->toBe($createWebhookResponse['enabled']);
    });

    it('throws an exception when the webhook is not found (404)', function (): void {
        expect(fn () => $this->asaasClient->webhook()->get('invalid-id'))->toThrow(NotFoundException::class, 'Resource not found');
    });

    it('throws an exception when the webhook ID is empty', function (): void {
        expect(fn () => $this->asaasClient->webhook()->get(''))->toThrow(\InvalidArgumentException::class, 'Webhook ID cannot be empty');
    });

    it('matches the expected response structure', function (): void {
        $id = uniqid();
        $createWebhookResponse = $this->asaasClient->webhook()->create([
            'name' => 'My Second Test Webhook'.$id,
            'url' => 'https://example.com/webhook-test-'.$id,
            'enabled' => true,
            'email' => 'iEo0Q@example.com',
            'sendType' => SendTypeEnum::Sequentially->value,
            'events' => [
                EventEnum::PaymentCreated->value,
                EventEnum::PaymentDeleted->value,
            ],
        ]);

        $response = $this->asaasClient->webhook()->get($createWebhookResponse['id']);
        expect($response)->toBeArray()
            ->and($response['id'])->toBe($createWebhookResponse['id'])
            ->and($response)->toHaveKeys(WEBHOOK_KEYS);
    });
});
