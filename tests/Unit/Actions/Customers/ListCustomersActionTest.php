<?php

use AsaasPhpSdk\Actions\Customers\ListCustomersAction;
use AsaasPhpSdk\DTOs\Customers\ListCustomersDTO;
use AsaasPhpSdk\Support\Http\Interface\HttpTransporterInterface;

describe('List Customers Action', function (): void {
    beforeEach(function (): void {
        // Mockamos a interface do Transporter
        $this->transporter = Mockery::mock(HttpTransporterInterface::class);
        $this->action = new ListCustomersAction($this->transporter);
    });

    it('lists customers successfully (200)', function (): void {
        // Dados que o DTO vai gerar
        $filters = [
            'limit' => 2,
            'offset' => 0,
            'name' => 'Maria',
        ];

        $expectedResponse = [
            'object' => 'list',
            'totalCount' => 2,
            'data' => [
                ['id' => 'cus_001', 'name' => 'Maria Oliveira'],
                ['id' => 'cus_002', 'name' => 'João Souza'],
            ],
        ];

        // Verificamos se a Action chama o Transporter com o array de filtros
        // A lógica de transformar isso em ?limit=2... agora é responsabilidade do Transporter
        $this->transporter->shouldReceive('send')
            ->once()
            ->with('GET', 'customers', $filters)
            ->andReturn($expectedResponse);

        $dto = ListCustomersDTO::fromArray($filters);
        $result = $this->action->handle($dto);

        expect($result)->toBe($expectedResponse)
            ->and($result['data'][0]['id'])->toBe('cus_001');
    });

    // Removidos os testes de 400 e Connection Error (já testados no Transporter/Handler)
    // Se quiser manter um teste de erro, foque apenas em um caso genérico:

    it('bubbles up exceptions from transporter', function (): void {
        $this->transporter->shouldReceive('send')
            ->andThrow(new \AsaasPhpSdk\Exceptions\Api\ApiException('Any error'));

        $dto = ListCustomersDTO::fromArray(['limit' => 10]);

        expect(fn() => $this->action->handle($dto))
            ->toThrow(\AsaasPhpSdk\Exceptions\Api\ApiException::class, 'Any error');
    });
});
