<?php

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

    // 1. Mock of Request
    $requestMock = Mockery::mock(RequestInterface::class);
    $responseMock = Mockery::mock(ResponseInterface::class);
    $streamMock = Mockery::mock(StreamInterface::class);

    // Expect : Create Request
    $this->requestFactory->shouldReceive('createRequest')
        ->once()
        ->with('POST', $path)
        ->andReturn($requestMock);

    // Expect: Create Stream
    $this->streamFactory->shouldReceive('createStream')
        ->once()
        ->with(json_encode($data))
        ->andReturn($streamMock);

    //Expect : Add body
    $requestMock->shouldReceive('withBody')
        ->once()
        ->with($streamMock)
        ->andReturnSelf();

    // Expect: Send Request
    $this->client->shouldReceive('sendRequest')
        ->once()
        ->with($requestMock)
        ->andReturn($responseMock);

    // Expect: Handle Response
    $this->responseHandler->shouldReceive('handle')
        ->once()
        ->with($responseMock)
        ->andReturn($expectedResponse);

    // Execute
    $result = $this->transporter->send('POST', $path, $data);

    expect($result)->toBe($expectedResponse);
});
