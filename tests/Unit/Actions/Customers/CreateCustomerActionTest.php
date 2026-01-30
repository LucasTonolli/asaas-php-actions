<?php

use AsaasPhpSdk\Actions\Customers\CreateCustomerAction;
use AsaasPhpSdk\DTOs\Customers\CreateCustomerDTO;
use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;
use AsaasPhpSdk\Support\Http\Interface\HttpTransporterInterface;


describe('Create Customer Action', function (): void {

    beforeEach(function () {
        $this->transporter = Mockery::mock(HttpTransporterInterface::class);
        $this->action = new CreateCustomerAction($this->transporter);

        $this->dto = CreateCustomerDTO::fromArray([
            'name' => 'João Silva',
            'cpfCnpj' => '111.444.777-35',
        ]);
    });

    it('creates customer successfully', function (): void {
        $expectedData = [
            'name' => 'João Silva',
            'cpfCnpj' => '11144477735',
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('POST', 'customers', $expectedData)
            ->andReturn(['id' => 'cus_123', 'name' => 'João Silva', 'cpfCnpj' => '11144477735']);


        $result = $this->action->handle($this->dto);

        expect($result['id'])->toBe('cus_123');
    });

    it('throws ValidationException when transporter reports validation error', function (): void {
        $this->transporter->shouldReceive('send')
            ->once()
            ->andThrow(new ValidationException('CPF is invalid'));

        expect(fn() => $this->action->handle($this->dto))
            ->toThrow(ValidationException::class, 'CPF is invalid');
    });

    it('throws ApiException on transporter network failure', function (): void {
        $this->transporter->shouldReceive('send')
            ->once()
            ->andThrow(new ApiException('Failed to connect to Asaas API: Connection failed'));

        expect(fn() => $this->action->handle($this->dto))
            ->toThrow(ApiException::class, 'Failed to connect to Asaas API: Connection failed');
    });
});
