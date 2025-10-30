<?php

use AsaasPhpSdk\Actions\CreditCard\TokenizationAction;
use AsaasPhpSdk\DTOs\CreditCard\TokenizationDTO;
use AsaasPhpSdk\Exceptions\Api\ValidationException;
use AsaasPhpSdk\Support\Helpers\ResponseHandler;

describe('Tokenization Action', function (): void {
    it('tokenizes a credit card successfully', function (): void {
        $client = mockClient([
            mockResponse([
                'creditCardNumber' => '4242',
                'creditCardBrand' => 'VISA',
                'creditCardToken' => 'a75a1d98-c52d-4a6b-a413-71e00b193c99',
            ], 200),
        ]);

        $action = new TokenizationAction($client, new ResponseHandler);
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

        $result = $action->handle($dto);
        expect($result)->toBeArray()
            ->and($result['creditCardNumber'])->toBe('4242')
            ->and($result['creditCardBrand'])->toBe('VISA')
            ->and($result['creditCardToken'])->toBe('a75a1d98-c52d-4a6b-a413-71e00b193c99');
    });

    it('throws a ValidationException on 400 error', function (): void {
        $client = mockClient([
            mockErrorResponse('Invalid credit card number', 400, [
                ['description' => 'Credit card number is invalid'],
            ]),
        ]);

        $action = new TokenizationAction($client, new ResponseHandler);
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

        expect(fn () => $action->handle($dto))->toThrow(ValidationException::class);
    });
});
