# 🛠️ Helpers

A camada de **Helpers** é composta por um conjunto de classes utilitárias, **stateless** (sem estado) e reutilizáveis, que fornecem suporte para as principais camadas do SDK (`Services`, `Actions`, `DTOs`).

Elas encapsulam lógicas de "baixo nível" e tarefas transversais (como sanitização, configuração de HTTP e tratamento de respostas), mantendo o resto do código limpo e focado em suas responsabilidades de negócio.

---

## 🧭 Princípios Fundamentais

- **Responsabilidade Única:** Cada classe `Helper` tem um propósito claro e bem definido. `DataSanitizer` só sanitiza dados, `HttpClientFactory` só cria clientes HTTP.
- **Sem Estado (Stateless):** Helpers não armazenam informações entre chamadas. Seus métodos operam apenas com os dados que recebem como entrada, e por isso são, em sua maioria, estáticos.
- **Reutilização:** São projetados para serem usados em múltiplos contextos dentro do SDK.
- **Abstração de Complexidade:** Eles escondem detalhes de implementação complexos, como a configuração de _middlewares_ do Guzzle ou a lógica de parsing de respostas de erro da API.

---

## 💡 Helpers Principais do SDK

### 1\. `DataSanitizer`

Esta classe é uma biblioteca de métodos estáticos e puros, focada em limpar e normalizar dados brutos. É amplamente utilizada dentro dos DTOs para garantir que os dados estejam em um formato previsível antes da validação. Todos os seus métodos são projetados para lidar com `null` de forma segura, retornando `null` se a entrada for nula.

**Responsabilidades:**

- Remover caracteres não numéricos (`onlyDigits`).
- Ajustar e normalizar strings (`sanitizeString`, `sanitizeLowercase`).
- Converter valores para tipos específicos de forma segura (`sanitizeBoolean`, `sanitizeInteger`, `sanitizeFloat`).

**Exemplo de uso:**

```php
// Dentro de um DTO
protected static function sanitize(array $data): array
{
    return [
        'document' => DataSanitizer::onlyDigits($data['document'] ?? null),
        'email' => DataSanitizer::sanitizeEmail($data['email'] ?? null),
        'notify' => DataSanitizer::sanitizeBoolean($data['notify'] ?? null),
    ];
}
```

### 2\. `HttpClientFactory`

É uma **Factory** cujo único objetivo é construir e configurar uma instância do `GuzzleHttp\Client`. Ela centraliza toda a configuração do cliente HTTP.

**Responsabilidades:**

- Definir a URL base (`base_uri`) e os timeouts.
- Inserir os cabeçalhos padrão em todas as requisições (`access_token`, `User-Agent`, etc.).
- Desabilitar a opção `http_errors` do Guzzle, delegando o tratamento de erros para o `ResponseHandler`.
- **Configurar Middlewares cruciais:**
  - **Retry Middleware:** Implementa uma lógica de novas tentativas automáticas (até 3 vezes) para falhas de conexão ou erros `5xx` da API e `429 Too Many Requests`. A espera entre as tentativas aumenta linearmente (`1s`, `2s`, `3s`).
  - **Logging Middleware:** Quando em ambiente `sandbox` e com logs habilitados na `AsaasConfig`, este middleware registra os detalhes de cada requisição (`método`, `URI`, `body`) no `error_log` do PHP, facilitando a depuração.

### 3\. `ResponseHandler`

Esta classe é a espinha dorsal da **estratégia de tratamento de erros** do SDK. Ela recebe a resposta HTTP do Guzzle e a traduz para o domínio da aplicação.

**Responsabilidades:**

- Verificar o `status code` da resposta.
- Para respostas de sucesso ( `2xx` ), extrai e retorna o corpo (`body`) da resposta como um `array`. Se o `body` estiver vazio ou for um JSON inválido, lança uma `ApiException`.
- Para respostas de erro ( `4xx` , `5xx` ), **converte o erro HTTP em uma exceção PHP específica e tipada** (ex: um erro `404` vira uma `NotFoundException`).
- Usa métodos internos como `extractErrorMessage()` e `extractRetryAfter()` para enriquecer as exceções com dados úteis do corpo e dos cabeçalhos da resposta.

### 4\. `EnumEnhancements` (Trait)

Este é um `trait` que adiciona um conjunto de funcionalidades poderosas a todos os `Enums` do SDK, tornando-os mais flexíveis e fáceis de usar.

**Funcionalidades Adicionadas:**

-   `tryFromString(string $value): ?static`: Constrói um `case` do Enum a partir de uma `string`, retornando `null` se o valor for inválido. É mais seguro que o `from()` nativo.
-   `all(): array`: Retorna um array com todos os `cases` do Enum.
-   `options(): array`: Retorna um array associativo `[key => label]`, ideal para preencher `<select>` em UIs.

**Como Usar:**

Para que o `trait` funcione, o Enum que o utiliza deve implementar dois métodos:

1.  `label(): string`: Um método público que retorna uma "etiqueta" humanamente legível para cada `case`.
2.  `fromString(string $value): self`: Um método `private static` que contém a lógica para converter uma `string` em um `case` do Enum, permitindo o mapeamento de múltiplos valores (ex: "credit_card", "Cartão de Crédito") para um único `case`.

**Exemplo Completo:**

```php
// src/DTOs/Payments/Enums/BillingTypeEnum.php

enum BillingTypeEnum: string
{
    // 1. Incluir o trait
    use EnumEnhancements;

    case Boleto = 'BOLETO';
    case CreditCard = 'CREDIT_CARD';

    // 2. Implementar o método label()
    public function label(): string
    {
        return match ($this) {
            self::Boleto => 'Boleto',
            self::CreditCard => 'Cartão de Crédito',
        };
    }

    // 3. Implementar a lógica de conversão
    private static function fromString(string $value): self
    {
        $normalized = DataSanitizer::sanitizeLowercase($value);

        return match (true) {
            in_array($normalized, ['boleto', 'ticket']) => self::Boleto,
            in_array($normalized, ['credit_card', 'cartão de crédito']) => self::CreditCard,
            default => throw new \ValueError("Invalid billing type '{$value}'"),
        };
    }
}

// Como o SDK usa o helper:
BillingTypeEnum::tryFromString('credit_card'); // Retorna BillingTypeEnum::CreditCard
BillingTypeEnum::options(); // Retorna ['Boleto' => 'Boleto', 'CreditCard' => 'Cartão de Crédito']
```
