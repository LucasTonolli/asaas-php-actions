<?php

use AsaasPhpSdk\Actions\Webhooks\DeleteWebhookAction;
use AsaasPhpSdk\Support\Http\Interface\HttpTransporterInterface;

describe('DeleteWebhookAction', function (): void {
    beforeEach(function (): void {
        $this->transporter = Mockery::mock(HttpTransporterInterface::class);
        $this->action = new DeleteWebhookAction($this->transporter);
    });

    it('deletes a webhook successfully (200)', function (): void {
        $webhookId = 'wh_123456';
        $expectedData = [
            'id' => $webhookId,
            'deleted' => true,
            'object' => 'webhook',
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('DELETE', 'webhooks/'.rawurlencode($webhookId), [])
            ->andReturn($expectedData);

        $result = $this->action->handle($webhookId);

        expect($result)->toBeArray()
            ->and($result['id'])->toBe($webhookId)
            ->and($result['deleted'])->toBeTrue();
    });

    it('throws InvalidArgumentException when ID is empty', function (): void {
        expect(fn () => $this->action->handle(''))->toThrow(\InvalidArgumentException::class, 'Webhook ID cannot be empty');
    });
});
