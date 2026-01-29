<?php

declare(strict_types=1);

namespace AsaasPhpSdk;

use AsaasPhpSdk\Config\AsaasConfig;
use AsaasPhpSdk\Services\CreditCardService;
use AsaasPhpSdk\Services\CustomerService;
use AsaasPhpSdk\Services\PaymentService;
use AsaasPhpSdk\Services\WebhookService;
use AsaasPhpSdk\Support\Http\GuzzleClientFactory;
use AsaasPhpSdk\Support\Http\Interface\HttpClientFactoryInterface;
use AsaasPhpSdk\Support\Http\HttpTransporter;
use AsaasPhpSdk\Support\Http\ResponseHandler;
use Http\Discovery\Psr17FactoryDiscovery;

/**
 * The main entry point for interacting with the Asaas API.
 *
 * This facade provides access to all available services and centralizes 
 * the HTTP communication layer configuration.
 *
 * @example
 * ```php
 * $config = new AsaasConfig('api-token');
 * $asaas = new AsaasClient($config);
 * $customers = $asaas->customer()->list();
 * ```
 */
final class AsaasClient
{
    private HttpTransporter $transporter;

    private ?CustomerService $customerService = null;

    private ?PaymentService $paymentService = null;

    private ?CreditCardService $creditCardService = null;

    private ?WebhookService $webhookService = null;

    /**
     * Initializes the Asaas SDK Client.
     *
     * @param AsaasConfig $config Environment and authentication settings.
     * @param HttpClientFactoryInterface|null $factory Optional custom factory for the HTTP Client. 
     * If null, GuzzleClientFactory will be used by default.
     */
    public function __construct(private readonly AsaasConfig $config, private HttpClientFactoryInterface|null $factory)
    {
        $this->factory ??= new GuzzleClientFactory($this->config);
        $this->transporter = $this->buildTransporter();
    }

    /**
     * Gets the Customer service handler.
     *
     * The service is lazy-loaded: it is instantiated on the first call and the
     * same instance is reused for all subsequent calls.
     *
     * @return CustomerService An instance of the CustomerService.
     */
    // public function customer(): CustomerService
    // {
    //     if ($this->customerService !== null) {
    //         return $this->customerService;
    //     }
    //     $this->customerService = new CustomerService($this->httpClient);

    //     return $this->customerService;
    // }

    // public function payment(): PaymentService
    // {
    //     if ($this->paymentService !== null) {
    //         return $this->paymentService;
    //     }
    //     $this->paymentService = new PaymentService($this->httpClient);

    //     return $this->paymentService;
    // }

    /**
     * Gets the CreditCard service handler.
     *
     * The service is lazy-loaded: it is instantiated on the first call and the
     * same instance is reused for all subsequent calls.
     *
     * @return CreditCardService An instance of the CreditCardService
     */
    // public function creditCard(): CreditCardService
    // {
    //     if ($this->creditCardService !== null) {
    //         return $this->creditCardService;
    //     }
    //     $this->creditCardService = new CreditCardService($this->httpClient);

    //     return $this->creditCardService;
    // }

    /**
     * Gets the Webhook service handler.
     *
     * The service is lazy-loaded: it is instantiated on the first call and the
     * same instance is reused for all subsequent calls.
     *
     * @return WebhookService An instance of the WebhookService
     */
    // public function webhook(): WebhookService
    // {
    //     if ($this->webhookService !== null) {
    //         return $this->webhookService;
    //     }
    //     $this->webhookService = new WebhookService($this->httpClient);

    //     return $this->webhookService;
    // }

    /**
     * Returns the configuration instance used by this client.
     */
    public function config(): AsaasConfig
    {
        return $this->config;
    }

    /**
     * Builds the internal transporter using PSR-17 discovery.
     * * @return HttpTransporter
     * @throws \Http\Discovery\Exception\DiscoveryFailedException If no PSR-17 factories are found.
     */
    private function buildTransporter(): HttpTransporter
    {

        return new HttpTransporter(
            $this->factory->create(),
            Psr17FactoryDiscovery::findRequestFactory(),
            Psr17FactoryDiscovery::findStreamFactory(),
            new ResponseHandler()
        );
    }

    /**
     * Helper to check if the current environment is Sandbox.
     */
    public function isSandbox(): bool
    {
        return $this->config->isSandbox();
    }
}
