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

    // 1. Mock da Request
    $requestMock = Mockery::mock(RequestInterface::class);
    $responseMock = Mockery::mock(ResponseInterface::class);
    $streamMock = Mockery::mock(StreamInterface::class);

    // Expectativa: Criar a request
    $this->requestFactory->shouldReceive('createRequest')
        ->once()
        ->with('POST', $path)
        ->andReturn($requestMock);

    // Expectativa: Criar o Stream do Body
    $this->streamFactory->shouldReceive('createStream')
        ->once()
        ->with(json_encode($data))
        ->andReturn($streamMock);

    // Expectativa: Vincular Body à Request
    $requestMock->shouldReceive('withBody')
        ->once()
        ->with($streamMock)
        ->andReturnSelf();

    // Expectativa: Cliente envia e recebe response
    $this->client->shouldReceive('sendRequest')
        ->once()
        ->with($requestMock)
        ->andReturn($responseMock);

    // Expectativa: Handler processa a response
    $this->responseHandler->shouldReceive('handle')
        ->once()
        ->with($responseMock)
        ->andReturn($expectedResponse);

    // Execução
    $result = $this->transporter->send('POST', $path, $data);

    // Verificação
    expect($result)->toBe($expectedResponse);
});
