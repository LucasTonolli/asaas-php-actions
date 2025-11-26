# 🎬 Actions

As **Actions** são a camada de fronteira (`boundary layer`) do SDK. Elas atuam como a **ponte entre o mundo interno e estruturado (DTOs, VOs) e o mundo externo (a API HTTP)**.

A principal responsabilidade de uma `Action` é traduzir um DTO de entrada (ou um ID) em uma requisição HTTP específica e executar essa chamada de forma segura e padronizada.

---

## 📌 Estrutura e Convenções

- **Padrão:** `{Verbo}{Recurso}Action`
- **Namespace:** `AsaasPhpSdk\Actions\{Recurso}`
- **Localização:** `src/Actions/{Recurso}/{Verbo}{Recurso}Action.php`

## ⚙️ A Classe Abstrata (`AbstractAction`)

Toda `Action` deve estender a `AbstractAction`. Esta classe base é crucial, pois centraliza duas responsabilidades críticas: a **execução da requisição** e o **tratamento padronizado de erros**.

Ela injeta o `Client` HTTP e um `ResponseHandler`, que é responsável por interpretar a resposta da API.

O método mais importante é o `executeRequest(callable $request)`. Ele funciona como um invólucro de segurança (`wrapper`) que:

1.  Executa a chamada HTTP que foi passada como um `callable`.
2.  Captura exceções comuns de rede do Guzzle (`RequestException`, `ConnectException`, etc.).
3.  Delega a resposta (seja de sucesso ou erro com corpo) para o `ResponseHandler`. **É neste ponto que a normalização de erros da API acontece: o `ResponseHandler` converte status codes HTTP como `4xx` e `5xx` em exceções específicas e tipadas. Por exemplo, uma resposta `429 Too Many Requests` é transformada em uma `RateLimitException`, que pode incluir o tempo de espera (`retry-after`).**
4.  Converte exceções de rede não tratadas em uma `ApiException` padronizada, garantindo que o SDK sempre comunique falhas de forma consistente.

Isso remove a necessidade de ter blocos `try/catch` para status codes repetidos em cada `Action`, tornando o código mais limpo e seguro.

---

## 🏗️ Base Actions para Operações Comuns

Para operações CRUD comuns, existem classes base abstratas que você deve estender para evitar a duplicação de lógica:

-   `GetByIdAction`: Para recuperar um recurso por ID (`GET /recurso/{id}`).
-   `DeleteByIdAction`: Para deletar um recurso por ID (`DELETE /recurso/{id}`).
-   `RestoreByIdAction`: Para restaurar um recurso por ID (`POST /recurso/{id}/restore`).

Quando você estende uma dessas classes, você só precisa implementar dois métodos:

-   `getResourceName()`: Retorna o nome do recurso (e.g., `'Customer'`, `'Payment'`). Usado para mensagens de erro padronizadas.
-   `getEndpoint(string $id)`: Retorna a string do endpoint formatada com o ID.

Esta abordagem encapsula a lógica de validação do ID e a chamada HTTP, tornando a `Action` concreta extremamente enxuta.

### ✨ Validação de ID com `ValidateResourceIdTrait`

As `Base Actions` acima utilizam o `ValidateResourceIdTrait`. Este trait fornece o método `validateAndNormalizeId()`, que garante que o ID de um recurso não seja uma string vazia antes de fazer a chamada à API, lançando uma `InvalidArgumentException` se a validação falhar.

Você pode usar este trait em qualquer `Action` que receba um ID de recurso.

---

## 🧭 Regras de Implementação

-   Toda `Action` deve **estender `AbstractAction`** (ou uma de suas filhas, como `GetByIdAction`).
-   Toda `Action` que recebe dados complexos deve **utilizar um DTO** como parâmetro de entrada.
-   O método principal deve se chamar `handle()`.
-   O método `handle()` deve **sempre retornar um `array`**, que é o resultado padronizado processado pelo `ResponseHandler`.

---

## ✅ Exemplos

### Ação Simples (com DTO)

```php
// src/Actions/Customers/CreateCustomerAction.php

namespace AsaasPhpSdk\Actions\Customers;

use AsaasPhpSdk\Actions\Base\AbstractAction;
use AsaasPhpSdk\DTOs\Customers\CreateCustomerDTO;

final class CreateCustomerAction extends AbstractAction
{
    public function handle(CreateCustomerDTO $data): array
    {
        // O método executeRequest cuida de toda a lógica de try/catch e
        // tratamento de erros, mantendo a Action limpa e focada.
        return $this->executeRequest(
            fn() => $this->client->post('customers', [
                'json' => $data->toArray(),
            ])
        );
    }
}
```

### Ação com `GetByIdAction`

Este exemplo mostra como é simples criar uma `Action` para buscar um recurso por ID.

```php
// src/Actions/Customers/GetCustomerAction.php

namespace AsaasPhpSdk\Actions\Customers;

use AsaasPhpSdk\Actions\Base\GetByIdAction;

final class GetCustomerAction extends GetByIdAction
{
    protected function getResourceName(): string
    {
        return 'Customer';
    }

    protected function getEndpoint(string $id): string
    {
        return 'customers/' . rawurlencode($id);
    }
}
```

