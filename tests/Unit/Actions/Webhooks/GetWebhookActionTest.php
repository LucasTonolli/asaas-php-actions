<?php

use AsaasPhpSdk\Actions\Webhooks\GetWebhookAction;
use AsaasPhpSdk\DTOs\Webhooks\Enums\EventEnum;
use AsaasPhpSdk\DTOs\Webhooks\Enums\SendTypeEnum;
use AsaasPhpSdk\Support\Http\Interface\HttpTransporterInterface;

describe('GetWebhookAction', function (): void {
    beforeEach(function (): void {
        $this->transporter = Mockery::mock(HttpTransporterInterface::class);
        $this->action = new GetWebhookAction($this->transporter);
    });

    it('retrieves a webhook successfully (200)', function (): void {
        $webhookId = 'wh_123456';
        $expectedData = [
            'id' => $webhookId,
            'name' => 'My Webhook',
            'url' => 'https://example.com/webhook',
            'enabled' => true,
            'interrupted' => false,
            'apiVersion' => 3,
            'sendType' => SendTypeEnum::Sequentially->value,
            'events' => [
                EventEnum::PaymentReceived->value,
                EventEnum::PaymentAnticipated->value,
            ],
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('GET', 'webhooks/'.$webhookId, [])
            ->andReturn($expectedData);

        $result = $this->action->handle($webhookId);

        expect($result)->toBeArray()
            ->and($result['id'])->toBe($webhookId)
            ->and($result['name'])->toBe('My Webhook');
    });

    it('throws InvalidArgumentException when ID is empty', function (): void {
        expect(fn () => $this->action->handle(''))->toThrow(\InvalidArgumentException::class, 'Webhook ID cannot be empty');
    });
});
