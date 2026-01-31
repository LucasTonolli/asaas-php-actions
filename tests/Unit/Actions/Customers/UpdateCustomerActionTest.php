<?php

use AsaasPhpSdk\Actions\Customers\UpdateCustomerAction;
use AsaasPhpSdk\DTOs\Customers\UpdateCustomerDTO;
use AsaasPhpSdk\Support\Http\Interface\HttpTransporterInterface;

describe('Update Customer Action', function (): void {
    beforeEach(function (): void {
        $this->transporter = Mockery::mock(HttpTransporterInterface::class);
        $this->action = new UpdateCustomerAction($this->transporter);
    });

    it('updates a customer successfully (200)', function (): void {
        $customerId = 'cus_123';
        $updateData = ['name' => 'João V. Silva'];

        $expectedResponse = [
            'id' => $customerId,
            'name' => 'João V. Silva',
            'cpfCnpj' => '89887966088',
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('PUT', "customers/{$customerId}", $updateData)
            ->andReturn($expectedResponse);

        $dto = UpdateCustomerDTO::fromArray($updateData);
        $result = $this->action->handle($customerId, $dto);

        expect($result)->toBe($expectedResponse)
            ->and($result['name'])->toBe('João V. Silva');
    });

    it('throws InvalidArgumentException when ID is empty', function (): void {
        $dto = UpdateCustomerDTO::fromArray(['name' => 'João Silva']);

        expect(fn() => $this->action->handle('', $dto))
            ->toThrow(\InvalidArgumentException::class, 'Customer ID cannot be empty');
    });
});
