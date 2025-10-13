<?php

declare(strict_types=1);

use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\RateLimitException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;
use AsaasPhpSdk\Helpers\ResponseHandler;

describe('ResponseHandler', function (): void {

    beforeEach(function (): void {
        $this->handler = new ResponseHandler;
    });

    it('handles successful 200 response', function (): void {
        $response = mockResponse([
            'id' => 'cus_123',
            'name' => 'João Silva',
        ], 200);

        $result = $this->handler->handle($response);

        expect($result)->toBeArray()
            ->and($result['id'])->toBe('cus_123')
            ->and($result['name'])->toBe('João Silva');
    });

    it('handles successful 201 response', function (): void {
        $response = mockResponse([
            'id' => 'cus_123',
            'created' => true,
        ], 201);

        $result = $this->handler->handle($response);

        expect($result)->toBeArray()
            ->and($result['id'])->toBe('cus_123');
    });

    it('handles empty successful response', function (): void {
        $response = mockResponse([], 204);

        $result = $this->handler->handle($response);

        expect($result)->toBeArray()
            ->and($result)->toBeEmpty();
    });

    it('throws AuthenticationException on 401', function (): void {
        $response = mockErrorResponse('Invalid API token', 401);

        $this->handler->handle($response);
    })->throws(AuthenticationException::class, 'Invalid API token');

    it('throws ValidationException on 400', function (): void {
        $response = mockErrorResponse('Invalid data', 400, [
            ['description' => 'Name is required'],
            ['description' => 'Email is invalid'],
        ]);

        $this->handler->handle($response);
    })->throws(ValidationException::class);

    it('throws NotFoundException on 404', function (): void {
        $response = mockErrorResponse('Customer not found', 404, [
            ['description' => 'Customer not found'],
        ]);

        $this->handler->handle($response);
    })->throws(NotFoundException::class, 'Customer not found');

    it('throws RateLimitException on 429', function (): void {
        $response = mockErrorResponse('Rate limit exceeded', 429);

        $this->handler->handle($response);
    })->throws(RateLimitException::class, 'Rate limit exceeded');

    it('throws ApiException on 500', function (): void {
        $response = mockErrorResponse('Internal server error', 500, [
            ['description' => 'Internal server error'],
        ]);

        $this->handler->handle($response);
    })->throws(ApiException::class, 'Internal server error');

    it('throws ApiException on 503', function (): void {
        $response = mockErrorResponse('Service unavailable', 503, [
            ['description' => 'Service unavailable'],
        ]);

        $this->handler->handle($response);
    })->throws(ApiException::class, 'Service unavailable');

    it('extracts error message from message field', function (): void {
        $response = mockErrorResponse('Custom error message', 400, [
            ['description' => 'Custom error message'],
        ]);

        try {
            $this->handler->handle($response);
        } catch (ValidationException $e) {
            expect($e->getMessage())->toBe('Custom error message');
        }
    });

    it('extracts multiple error messages from errors array', function (): void {
        $response = mockErrorResponse('Validation failed', 400, [
            ['description' => 'Name is required'],
            ['description' => 'Email is invalid'],
        ]);

        try {
            $this->handler->handle($response);
        } catch (ValidationException $e) {
            expect($e->getMessage())->toContain('Name is required')
                ->and($e->getMessage())->toContain('Email is invalid');
        }
    });

    it('throws ApiException for invalid JSON response', function (): void {
        $response = new GuzzleHttp\Psr7\Response(
            200,
            ['Content-Type' => 'application/json'],
            'invalid json{'
        );

        $this->handler->handle($response);
    })->throws(ApiException::class, 'Invalid JSON');

    it('handles response with no error message', function (): void {
        $response = new GuzzleHttp\Psr7\Response(
            400,
            ['Content-Type' => 'application/json'],
            json_encode([])
        );

        try {
            $this->handler->handle($response);
        } catch (ValidationException $e) {
            expect($e->getMessage())->toContain('Invalid data provided');
        }
    });
});
