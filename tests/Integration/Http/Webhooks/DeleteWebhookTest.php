<?php

use AsaasPhpSdk\DTOs\Webhooks\Enums\EventEnum;
use AsaasPhpSdk\DTOs\Webhooks\Enums\SendTypeEnum;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;

describe('Delete Webhook', function (): void {
    beforeEach(function (): void {
        $config = sandboxConfig();
        $this->asaasClient = new AsaasPhpSdk\AsaasClient($config);
    });

    it('deletes a webhook successfully (200)', function (): void {
        // Arrange
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

        // Act
        $deleteResponse = $this->asaasClient->webhook()->delete($createWebhookResponse['id']);

        // Assert
        expect($deleteResponse)->toBeArray()
            ->and($deleteResponse['deleted'])->toBeTrue()
            ->and($deleteResponse['id'])->toBe($createWebhookResponse['id']);

        // Verify that the webhook is actually gone
        expect(fn () => $this->asaasClient->webhook()->get($createWebhookResponse['id']))
            ->toThrow(NotFoundException::class, 'Resource not found');
    });

    it('throws an exception when trying to delete a non-existent webhook (404)', function (): void {
        expect(fn () => $this->asaasClient->webhook()->delete('wh_nonexistent'))
            ->toThrow(NotFoundException::class, 'Resource not found');
    });

    it('throws an exception when the webhook ID is empty', function (): void {
        expect(fn () => $this->asaasClient->webhook()->delete(''))
            ->toThrow(InvalidArgumentException::class, 'Webhook ID cannot be empty');
    });
});
