<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Support\Http\Interface;

use Psr\Http\Client\ClientInterface;

interface HttpClientFactoryInterface
{
	public function create(): ClientInterface;
}
