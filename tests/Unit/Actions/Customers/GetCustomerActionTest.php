<?php

use AsaasPhpSdk\Actions\Customers\GetCustomerAction;
use AsaasPhpSdk\Support\Http\Interface\HttpTransporterInterface;

describe('GetCustomerAction', function (): void {
    beforeEach(function (): void {
        $this->transporter = Mockery::mock(HttpTransporterInterface::class);
        $this->action = new GetCustomerAction($this->transporter);
    });

    it('retrieves a customer successfully (200)', function (): void {
        $customerId = 'cus_123';
        $expectedData = [
            'id' => $customerId,
            'name' => 'Maria Oliveira',
            'email' => 'maria@example.com',
            'cpfCnpj' => '12345678900',
            'object' => 'customer',
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('GET', 'customers/'.$customerId, [])
            ->andReturn($expectedData);

        $result = $this->action->handle($customerId);

        expect($result)->toBeArray()
            ->and($result['id'])->toBe($customerId)
            ->and($result['name'])->toBe('Maria Oliveira')
            ->and($result['object'])->toBe('customer');
    });

    it('throws InvalidArgumentException when ID is empty', function (): void {
        expect(fn () => $this->action->handle(''))->toThrow(\InvalidArgumentException::class, 'Customer ID cannot be empty');
    });
});
