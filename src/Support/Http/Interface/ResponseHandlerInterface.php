<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Support\Http\Interface;

use Psr\Http\Message\ResponseInterface;

interface ResponseHandlerInterface
{
	public function handle(ResponseInterface $response): array;
}
