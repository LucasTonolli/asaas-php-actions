<?php

use AsaasPhpSdk\Exceptions\Api\AuthenticationException;
use AsaasPhpSdk\Exceptions\Api\NotFoundException;
use AsaasPhpSdk\Exceptions\Api\ValidationException;
use AsaasPhpSdk\Support\Http\HttpTransporter;
use AsaasPhpSdk\Support\Http\Interface\ResponseHandlerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

beforeEach(function (): void {
    $this->client = Mockery::mock(ClientInterface::class);
    $this->requestFactory = Mockery::mock(RequestFactoryInterface::class);
    $this->streamFactory = Mockery::mock(StreamFactoryInterface::class);
    $this->responseHandler = Mockery::mock(ResponseHandlerInterface::class);

    $this->transporter = new HttpTransporter(
        $this->client,
        $this->requestFactory,
        $this->streamFactory,
        $this->responseHandler
    );
});

it('should send a request correctly and return an array in handler', function (): void {
    $path = 'customers';
    $data = ['name' => 'John Doe'];
    $expectedResponse = ['id' => 'cus_123'];

    $requestMock = Mockery::mock(RequestInterface::class);
    $responseMock = Mockery::mock(ResponseInterface::class);
    $streamMock = Mockery::mock(StreamInterface::class);

    $this->requestFactory->shouldReceive('createRequest')
        ->once()
        ->with('POST', $path)
        ->andReturn($requestMock);

    $this->streamFactory->shouldReceive('createStream')
        ->once()
        ->with(json_encode($data, JSON_THROW_ON_ERROR))
        ->andReturn($streamMock);

    $requestMock->shouldReceive('withBody')
        ->once()
        ->with($streamMock)
        ->andReturnSelf();

    $this->client->shouldReceive('sendRequest')
        ->once()
        ->with($requestMock)
        ->andReturn($responseMock);

    $this->responseHandler->shouldReceive('handle')
        ->once()
        ->with($responseMock)
        ->andReturn($expectedResponse);

    $result = $this->transporter->send('POST', $path, $data);

    expect($result)->toBe($expectedResponse);
});

it('bubbles up ValidationException from response handler', function (): void {
    $requestMock = Mockery::mock(RequestInterface::class);
    $responseMock = Mockery::mock(ResponseInterface::class);

    $this->requestFactory->shouldReceive('createRequest')->andReturn($requestMock);
    $this->client->shouldReceive('sendRequest')->andReturn($responseMock);

    $this->responseHandler->shouldReceive('handle')
        ->once()
        ->andThrow(new ValidationException('ID format is invalid'));

    expect(fn () => $this->transporter->send('GET', 'customers/invalid'))
        ->toThrow(ValidationException::class, 'ID format is invalid');
});

it('bubbles up AuthenticationException from response handler', function (): void {
    $requestMock = Mockery::mock(RequestInterface::class);
    $responseMock = Mockery::mock(ResponseInterface::class);

    $this->requestFactory->shouldReceive('createRequest')->andReturn($requestMock);
    $this->client->shouldReceive('sendRequest')->andReturn($responseMock);

    $this->responseHandler->shouldReceive('handle')
        ->once()
        ->andThrow(new AuthenticationException('Invalid API token'));

    expect(fn () => $this->transporter->send('GET', 'customers'))
        ->toThrow(AuthenticationException::class, 'Invalid API token');
});

it('bubbles up NotFoundException from response handler', function (): void {
    $requestMock = Mockery::mock(RequestInterface::class);
    $responseMock = Mockery::mock(ResponseInterface::class);

    $this->requestFactory->shouldReceive('createRequest')->andReturn($requestMock);
    $this->client->shouldReceive('sendRequest')->andReturn($responseMock);

    $this->responseHandler->shouldReceive('handle')
        ->once()
        ->andThrow(new NotFoundException('Resource not found'));

    expect(fn () => $this->transporter->send('GET', 'customers/notfound'))
        ->toThrow(NotFoundException::class, 'Resource not found');
});
