<?php

use AsaasPhpSdk\Actions\Customers\DeleteCustomerAction;
use AsaasPhpSdk\Support\Http\Interface\HttpTransporterInterface;

describe('Delete Customer Action', function (): void {
    beforeEach(function (): void {
        $this->transporter = Mockery::mock(HttpTransporterInterface::class);
        $this->action = new DeleteCustomerAction($this->transporter);
    });

    it('deletes a customer successfully (200)', function (): void {
        $expectedData = [
            'id' => 'cus_123',
            'deleted' => true,
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('DELETE', 'customers/cus_123', [])
            ->andReturn($expectedData);

        $result = $this->action->handle('cus_123');

        expect($result)->toBe($expectedData);
    });

    it('throws InvalidArgumentException when ID is empty', function (): void {
        expect(fn () => $this->action->handle(''))->toThrow(\InvalidArgumentException::class, 'Customer ID cannot be empty');
    });
});
