<?php

use AsaasPhpSdk\Exceptions\Api\ApiException;
use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\RateLimitException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;
use AsaasPhpSdk\Support\Http\ResponseHandler;
use GuzzleHttp\Psr7\Response;

beforeEach(function () {
    $this->handler = new ResponseHandler;
});

describe('ResponseHandler tests', function (): void {

    it('returns parsed array when response is successful', function () {
        $json = json_encode(['id' => 'cus_123', 'name' => 'John']);
        $response = new Response(200, [], $json);

        $result = $this->handler->handle($response);

        expect($result)->toBe(['id' => 'cus_123', 'name' => 'John']);
    });

    it('throws ValidationException when status is 400', function () {
        $json = json_encode([
            'errors' => [
                ['description' => 'Invalid CPF'],
            ],
        ]);
        $response = new Response(400, [], $json);

        expect(fn() => $this->handler->handle($response))
            ->toThrow(ValidationException::class, 'Invalid CPF');
    });

    it('throws AuthenticationException when status is 401', function () {
        $response = new Response(401, [], json_encode(['errors' => []]));

        expect(fn() => $this->handler->handle($response))
            ->toThrow(AuthenticationException::class);
    });

    it('throws NotFoundException when status is 404', function () {
        $response = new Response(404, [], json_encode(['errors' => []]));

        expect(fn() => $this->handler->handle($response))
            ->toThrow(NotFoundException::class);
    });

    it('throws RateLimitException  when status is 429', function () {
        $response = new Response(429, [], json_encode(['errors' => []]));

        expect(fn() => $this->handler->handle($response))
            ->toThrow(RateLimitException::class);
    });

    it('throws ApiException when status is 5xx', function () {
        $response = new Response(500, [], json_encode(['errors' => []]));

        expect(fn() => $this->handler->handle($response))
            ->toThrow(ApiException::class);
    });

    it('concatenates multiple error messages correctly', function () {
        $json = json_encode([
            'errors' => [
                ['description' => 'Error 1'],
                ['description' => 'Error 2'],
            ],
        ]);
        $response = new Response(400, [], $json);

        // Verifies if the handler joined messages with "; "
        expect(fn() => $this->handler->handle($response))
            ->toThrow(ValidationException::class, 'Error 1; Error 2');
    });

    it('throws ApiException for unexpected status codes', function () {
        $response = new Response(505, [], json_encode(['errors' => []]));

        expect(fn() => $this->handler->handle($response))
            ->toThrow(ApiException::class);
    });

    it('throws ApiException for JSON parse errors', function () {
        $response = new Response(200, [], 'invalid json');

        expect(fn() => $this->handler->handle($response))
            ->toThrow(ApiException::class);
    });
});
