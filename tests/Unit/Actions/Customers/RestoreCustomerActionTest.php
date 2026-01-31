<?php

use AsaasPhpSdk\Actions\Customers\RestoreCustomerAction;
use AsaasPhpSdk\Support\Helpers\ResponseHandler;
use AsaasPhpSdk\Support\Http\Interface\HttpTransporterInterface;

describe('Restore Customer Action', function (): void {

    beforeEach(function (): void {
        $this->transporter = Mockery::mock(HttpTransporterInterface::class);
        $this->action = new RestoreCustomerAction($this->transporter);
    });

    it('Restore a customer successfully (200)', function (): void {
        $expectedData = [
            'object' => 'customer',
            'id' => 'cus_123',
            'dateCreated' => '2023-06-01T00:00:00.000Z',
            'name' => 'John Doe',
            'deleted' => false,
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('POST', 'customers/cus_123/restore', [])
            ->andReturn($expectedData);
        $result = $this->action->handle('cus_123');

        expect($result)->toBeArray()
            ->and($result['deleted'])->toBeFalse()
            ->and($result['id'])->toBe('cus_123');
    });


    it('throws InvalidArgumentException when ID is empty', function (): void {
        expect(fn() => $this->action->handle(''))->toThrow(\InvalidArgumentException::class, 'Customer ID cannot be empty');
    });
});
