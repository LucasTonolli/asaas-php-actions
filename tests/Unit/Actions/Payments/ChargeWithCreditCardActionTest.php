<?php

use AsaasPhpSdk\Actions\Payments\ChargeWithCreditCardAction;
use AsaasPhpSdk\DTOs\Payments\ChargeWithCreditCardDTO;
use AsaasPhpSdk\Support\Http\Interface\HttpTransporterInterface;

describe('ChargeWithCreditCardAction', function (): void {
    beforeEach(function (): void {
        $this->transporter = Mockery::mock(HttpTransporterInterface::class);
        $this->action = new ChargeWithCreditCardAction($this->transporter);
    });

    it('charges a payment with credit card successfully (200)', function (): void {
        $paymentId = 'pay_123';
        $dto = ChargeWithCreditCardDTO::fromArray([
            'creditCard' => [
                'holderName' => 'John Doe',
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => (string) ((int) date('Y') + 1),
                'ccv' => '123',
            ],
            'creditCardHolderInfo' => [
                'name' => 'John Doe',
                'email' => 'john.doe@test.com',
                'cpfCnpj' => '824.121.180-51',
                'postalCode' => '00000-000',
                'phone' => '1234567890',
                'addressNumber' => '123',
            ],
        ]);

        $expectedData = [
            'id' => $paymentId,
            'customer' => 'cus_123',
            'value' => 150.75,
            'billingType' => 'CREDIT_CARD',
            'dueDate' => '2025-12-31',
            'status' => 'CONFIRMED',
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('POST', 'payments/' . $paymentId . '/payWithCreditCard', $dto->toArray())
            ->andReturn($expectedData);

        $result = $this->action->handle($paymentId, $dto);

        expect($result)->toBeArray()
            ->and($result['id'])->toBe($paymentId)
            ->and($result['customer'])->toBe('cus_123')
            ->and($result['value'])->toBe(150.75)
            ->and($result['billingType'])->toBe('CREDIT_CARD')
            ->and($result['dueDate'])->toBe('2025-12-31')
            ->and($result['status'])->toBe('CONFIRMED');
    });

    it('charges a payment with credit card token successfully (200)', function (): void {
        $paymentId = 'pay_123';
        $dto = ChargeWithCreditCardDTO::fromArray([
            'creditCardToken' => 'tok_12345',
        ]);

        $expectedData = [
            'id' => $paymentId,
            'customer' => 'cus_123',
            'value' => 150.75,
            'billingType' => 'CREDIT_CARD',
            'dueDate' => '2025-12-31',
            'status' => 'CONFIRMED',
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('POST', 'payments/' . $paymentId . '/payWithCreditCard', $dto->toArray())
            ->andReturn($expectedData);

        $result = $this->action->handle($paymentId, $dto);

        expect($result)->toBeArray()
            ->and($result['id'])->toBe($paymentId)
            ->and($result['customer'])->toBe('cus_123')
            ->and($result['value'])->toBe(150.75)
            ->and($result['billingType'])->toBe('CREDIT_CARD')
            ->and($result['dueDate'])->toBe('2025-12-31')
            ->and($result['status'])->toBe('CONFIRMED');
    });

    it('throws InvalidArgumentException when ID is empty', function (): void {
        $dto = ChargeWithCreditCardDTO::fromArray([
            'creditCard' => [
                'holderName' => 'John Doe',
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => (string) ((int) date('Y') + 1),
                'ccv' => '123',
            ],
            'creditCardHolderInfo' => [
                'name' => 'John Doe',
                'email' => 'john.doe@test.com',
                'cpfCnpj' => '824.121.180-51',
                'postalCode' => '00000-000',
                'phone' => '1234567890',
                'addressNumber' => '123',
            ],
        ]);

        expect(fn() => $this->action->handle('', $dto))->toThrow(\InvalidArgumentException::class, 'Payment ID cannot be empty');
    });
});
