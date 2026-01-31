<?php

use AsaasPhpSdk\Actions\CreditCard\TokenizationAction;
use AsaasPhpSdk\DTOs\CreditCard\TokenizationDTO;
use AsaasPhpSdk\Support\Http\Interface\HttpTransporterInterface;

describe('TokenizationAction', function (): void {
    beforeEach(function (): void {
        $this->transporter = Mockery::mock(HttpTransporterInterface::class);
        $this->action = new TokenizationAction($this->transporter);
    });

    it('tokenizes a credit card successfully (200)', function (): void {
        $dto = TokenizationDTO::fromArray([
            'customer' => 'cus_123',
            'creditCard' => [
                'holderName' => 'John Doe',
                'number' => '4242 4242 4242 4242',
                'expiryMonth' => '12',
                'expiryYear' => (string) ((int) date('Y') + 1),
                'ccv' => '123',
            ],
            'creditCardHolderInfo' => [
                'name' => 'John Doe',
                'email' => 'john.doe@test.com',
                'cpfCnpj' => '824.121.180-51',
                'postalCode' => '00000-000',
                'addressNumber' => '12345',
                'phone' => '1234567890',
                'mobilePhone' => '1234567890',
            ],
            'remoteIp' => '127.0.0.1',
        ]);

        $expectedData = [
            'creditCardNumber' => '4242',
            'creditCardBrand' => 'VISA',
            'creditCardToken' => 'a75a1d98-c52d-4a6b-a413-71e00b193c99',
        ];

        $this->transporter->shouldReceive('send')
            ->once()
            ->with('POST', 'creditCard/tokenizeCreditCard', $dto->toArray())
            ->andReturn($expectedData);

        $result = $this->action->handle($dto);

        expect($result)->toBeArray()
            ->and($result['creditCardNumber'])->toBe('4242')
            ->and($result['creditCardBrand'])->toBe('VISA')
            ->and($result['creditCardToken'])->toBe('a75a1d98-c52d-4a6b-a413-71e00b193c99');
    });
});
