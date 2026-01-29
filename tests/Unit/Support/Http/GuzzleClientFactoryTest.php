<?php

use AsaasPhpSdk\Config\AsaasConfig;
use AsaasPhpSdk\Support\Http\GuzzleClientFactory;

it('should create a client with the correct configuration', function () {
    $config = new AsaasConfig('token_teste', isSandbox: true);
    $factory = new GuzzleClientFactory($config);
    $client = $factory->create();
    $clientConfig = $client->getConfig();
    expect($clientConfig['headers']['access_token'])->toBe('token_teste');
    expect($clientConfig['base_uri']->getHost())->toBe('api-sandbox.asaas.com');
});
