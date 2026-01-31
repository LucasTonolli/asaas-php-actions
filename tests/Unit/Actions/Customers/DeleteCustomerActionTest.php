<?php

use AsaasPhpSdk\Actions\Customers\DeleteCustomerAction;
use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;
use AsaasPhpSdk\Support\Helpers\ResponseHandler;
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

    it('throws ValidationException on 400 error', function (): void {
        $this->transporter->shouldReceive('send')
            ->once()
            ->andThrow(new ValidationException('Customer cannot be deleted'));

        expect(fn() => $this->action->handle('cus_invalid'))
            ->toThrow(ValidationException::class, 'Customer cannot be deleted');
    });

    it('throws AuthenticationException on 401 error', function (): void {
        $this->transporter->shouldReceive('send')
            ->once()
            ->andThrow(new AuthenticationException('Invalid API token or unauthorized access'));

        expect(fn() => $this->action->handle('cus_invalid'))
            ->toThrow(AuthenticationException::class, 'Invalid API token or unauthorized access');
    });

    it('throws NotFoundException on 404 error', function (): void {
        $this->transporter->shouldReceive('send')
            ->once()
            ->andThrow(new NotFoundException('Resource not found'));

        expect(fn() => $this->action->handle('cus_notfound'))
            ->toThrow(NotFoundException::class, 'Resource not found');
    });

    it('throws InvalidArgumentException when ID is empty', function (): void {
        expect(fn() => $this->action->handle(''))->toThrow(\InvalidArgumentException::class, 'Customer ID cannot be empty');
    });
});
