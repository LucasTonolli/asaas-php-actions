<?php

use AsaasPhpSdk\Actions\Customers\UpdateCustomerAction;
use AsaasPhpSdk\DTOs\Customers\UpdateCustomerDTO;
use AsaasPhpSdk\Exceptions\Api\ValidationException;
use AsaasPhpSdk\Helpers\ResponseHandler;

describe('Update Customer Action', function (): void {

    it('update customer successfully', function (): void {
        $client = mockClient([
            mockResponse([
                'id' => 'cus_123',
                'name' => 'João V. Silva',
                'cpfCnpj' => '89887966088',
            ], 200),
        ]);

        $action = new UpdateCustomerAction($client, new ResponseHandler);

        $dto = UpdateCustomerDTO::fromArray([
            'name' => 'João V. Silva',
        ]);

        $result = $action->handle('cus_123', $dto);

        expect($result)->toBeArray()
            ->and($result['id'])->toBe('cus_123')
            ->and($result['name'])->toBe('João V. Silva')
            ->and($result['cpfCnpj'])->toBe('89887966088');
    });

    it('throws ValidationException on 400 error', function (): void {
        $client = mockClient([
            mockErrorResponse('Input validation failed', 400, [
                ['description' => 'CPF is invalid'],
            ]),
        ]);
        $action = new UpdateCustomerAction($client, new ResponseHandler);

        $dto = UpdateCustomerDTO::fromArray([
            'name' => 'João Silva',
            'cpfCnpj' => '11144477735',
        ]);

        $action->handle('cus_123', $dto);
    })->throws(ValidationException::class, 'CPF is invalid');
});
