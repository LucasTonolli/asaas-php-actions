<?php

use AsaasPhpSdk\Actions\Payments\GetPaymentTicketLineAction;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Support\Helpers\ResponseHandler;

describe('Get Payment Ticket Line Action', function (): void {
    it('retrieves payment ticket line successfully', function (): void {
        $paymentId = 'pay_123';

        $client = mockClient([
            mockResponse([
                'identificationField' => '1234567890',
                'nossoNumero' => '0987654321',
                'barCode' => '00190500954014481606906809350314337370000000100',
            ], 200),
        ]);

        $action = new GetPaymentTicketLineAction($client, new ResponseHandler);

        $result = $action->handle($paymentId);

        expect($result)->toBeArray()
            ->and($result)->toHaveKeys([
                'identificationField',
                'nossoNumero',
                'barCode',
            ]);
    });

    it('throws NotFoundException on 404 error', function (): void {
        $paymentId = 'pay_123';

        $client = mockClient([
            mockResponse([], 404),
        ]);

        $action = new GetPaymentTicketLineAction($client, new ResponseHandler);

        expect(fn() => $action->handle($paymentId))->toThrow(NotFoundException::class);
    });

    it('throws InvalidArgumentException when ID is empty', function (): void {
        $client = mockClient([]);
        $action = new GetPaymentTicketLineAction($client, new ResponseHandler);

        expect(fn() => $action->handle(''))->toThrow(\InvalidArgumentException::class);
    });
});
