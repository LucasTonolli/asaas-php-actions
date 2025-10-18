<?php

use AsaasPhpSdk\Actions\Payments\ListPaymentsAction;
use AsaasPhpSdk\DTOs\Payments\Enums\PaymentStatusEnum;
use AsaasPhpSdk\DTOs\Payments\ListPaymentsDTO;
use AsaasPhpSdk\Exceptions\Api\ValidationException;
use AsaasPhpSdk\Helpers\ResponseHandler;
use GuzzleHttp\Client;

const RESPONSE_KEYS = [
    'object',
    'hasMore',
    'totalCount',
    'offset',
    'limit',
    'data',
];

describe('List Payments Action', function (): void {

    it('lists payments successfully', function (): void {
        $client = mockClient([
            mockResponse([
                'object' => 'list',
                'hasMore' => false,
                'totalCount' => 1,
                'offset' => 0,
                'limit' => 5,
                'data' => [],
            ], 200),
        ]);

        $dto = ListPaymentsDTO::fromArray([
            'limit' => 5,
            'billingType' => 'BOLETO',
            'status' => 'PENDING',
        ]);

        $action = new ListPaymentsAction($client, new ResponseHandler);

        $response = $action->handle($dto);

        expect($response)->toBeArray()
            ->and($response)->toHaveKeys(RESPONSE_KEYS)
            ->and($response['data'])->toBeArray()
            ->and($response['limit'])->toBe(5);
    });

    it('maps DTO filters into correct query parameters for the List Payments endpoint', function (): void {
        $mockClient = Mockery::mock(Client::class);
        $mockClient
            ->shouldReceive('get')
            ->once()
            ->with(
                'payments',
                [
                    'query' => [
                        'limit' => 10,
                        'status' => 'CONFIRMED',
                        'dateCreated[ge]' => '2025-01-01',
                        'dateCreated[le]' => '2025-01-02',
                    ],
                ]
            )
            ->andReturn(new \GuzzleHttp\Psr7\Response(200, [], json_encode([
                'object' => 'list',
                'data' => [],
            ])));

        $action = new ListPaymentsAction($mockClient, new ResponseHandler);

        $dto = ListPaymentsDTO::fromArray([
            'limit' => 10,
            'status' => PaymentStatusEnum::Confirmed->value,
            'dateCreatedStart' => '2025-01-01',
            'dateCreatedEnd' => '2025-01-02',
        ]);

        // 2. Act
        $action->handle($dto);
    });

    it('throws ValidationException on API 400 error', function (): void {
        $client = mockClient([
            mockResponse(['error' => 'Bad Request'], 400),
        ]);

        $dto = ListPaymentsDTO::fromArray(['limit' => 5]);

        $action = new ListPaymentsAction($client, new ResponseHandler);

        expect(fn () => $action->handle($dto))
            ->toThrow(ValidationException::class);
    });
});
