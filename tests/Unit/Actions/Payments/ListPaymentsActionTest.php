<?php

use AsaasPhpSdk\Actions\Payments\ListPaymentsAction;
use AsaasPhpSdk\DTOs\Payments\Enums\PaymentStatusEnum;
use AsaasPhpSdk\DTOs\Payments\ListPaymentsDTO;
use AsaasPhpSdk\Support\Http\Interface\HttpTransporterInterface;

const RESPONSE_KEYS = [
    'object',
    'hasMore',
    'totalCount',
    'offset',
    'limit',
    'data',
];

describe('ListPaymentsAction', function (): void {
    beforeEach(function (): void {
        $this->transporter = Mockery::mock(HttpTransporterInterface::class);
        $this->action = new ListPaymentsAction($this->transporter);
    });

    it('lists payments successfully (200)', function (): void {
        $dto = ListPaymentsDTO::fromArray([
            'limit' => 5,
            'billingType' => 'BOLETO',
            'status' => 'PENDING',
        ]);

        $expectedData = [
            'object' => 'list',
            'hasMore' => false,
            'totalCount' => 1,
            'offset' => 0,
            'limit' => 5,
            'data' => [],
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('GET', 'payments', $dto->toArray())
            ->andReturn($expectedData);

        $response = $this->action->handle($dto);

        expect($response)->toBeArray()
            ->and($response)->toHaveKeys(RESPONSE_KEYS)
            ->and($response['data'])->toBeArray()
            ->and($response['limit'])->toBe(5);
    });

    it('sends correct query parameters for date filters', function (): void {
        $dto = ListPaymentsDTO::fromArray([
            'limit' => 10,
            'status' => PaymentStatusEnum::Confirmed->value,
            'dateCreatedStart' => '2025-01-01',
            'dateCreatedEnd' => '2025-01-02',
        ]);

        $expectedData = [
            'object' => 'list',
            'data' => [],
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('GET', 'payments', $dto->toArray())
            ->andReturn($expectedData);

        $this->action->handle($dto);
    });
});
