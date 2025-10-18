<?php

use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

describe('Get Payment Ticket Line', function (): void {
    beforeEach(function (): void {
        $config = sandboxConfig();
        $this->client = new AsaasPhpSdk\AsaasClient($config);
    });

    it('retrieves payment ticket line successfully', function (): void {
        $customerId = getDefaultCustomer();
        $ticketPayment = $this->client->payment()->create([
            'customer' => $customerId,
            'value' => 10.00,
            'billingType' => 'BOLETO',
            'dueDate' => date('Y-m-d', strtotime('+5 days')),
        ]);

        $result = $this->client->payment()->getTicketLine($ticketPayment['id']);

        expect($result)->toBeArray()
            ->and($result)->toHaveKeys([
                'identificationField',
                'nossoNumero',
                'barCode',
            ]);

        $this->client->payment()->delete($ticketPayment['id']);
    });

    it('throws NotFoundException on invalid payment ID', function (): void {
        $invalidPaymentId = 'pay_invalid_id';

        expect(fn () => $this->client->payment()->getTicketLine($invalidPaymentId))
            ->toThrow(NotFoundException::class, 'Resource not found');
    });

    it('throws InvalidArgumentException when ID is empty', function (): void {
        expect(fn () => $this->client->payment()->getTicketLine(''))
            ->toThrow(\InvalidArgumentException::class, 'Payment ID cannot be empty');
    });

    it('throws exception when payment is not a boleto', function (): void {
        $customerId = getDefaultCustomer();
        $creditCardPayment = $this->client->payment()->create([
            'customer' => $customerId,
            'value' => 20.00,
            'billingType' => 'CREDIT_CARD',
            'dueDate' => date('Y-m-d', strtotime('+5 days')),
        ]);

        expect(fn () => $this->client->payment()->getTicketLine($creditCardPayment['id']))
            ->toThrow(ValidationException::class, 'Somente é possível obter linha digitável quando a forma de pagamento for boleto bancário.');

        $this->client->payment()->delete($creditCardPayment['id']);
    });
});
