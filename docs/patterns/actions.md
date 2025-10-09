# 🎬 Actions

As **Actions** são a camada de fronteira (`boundary layer`) do SDK. Elas atuam como a **ponte entre o mundo interno e estruturado (DTOs, VOs) e o mundo externo (a API HTTP)**.

A principal responsabilidade de uma `Action` é traduzir um DTO de entrada, que já foi validado e tipado, em uma requisição HTTP específica e executar essa chamada de forma segura e padronizada.

---

## 📌 Estrutura e Convenções

- **Padrão:** `{Verbo}{Recurso}Action`
- **Namespace:** `AsaasPhpSdk\Actions\{Recurso}`
- **Localização:** `src/Actions/{Recurso}/{Verbo}{Recurso}Action.php`

---

## ⚙️ A Classe Abstrata (`AbstractAction`)

Toda `Action` deve estender a `AbstractAction`. Esta classe base é crucial, pois centraliza duas responsabilidades críticas: a **execução da requisição** e o **tratamento padronizado de erros**.

Ela injeta o `Client` HTTP e um `ResponseHandler`, que é responsável por interpretar a resposta da API.

O método mais importante é o `executeRequest(callable $request)`. Ele funciona como um invólucro de segurança (`wrapper`) que:

1.  Executa a chamada HTTP que foi passada como um `callable`.
2.  Captura exceções comuns do Guzzle (`RequestException`, `ConnectException`, etc.).
3.  Delega a resposta (seja de sucesso ou erro com corpo) para o `ResponseHandler`.
4.  Converte exceções de rede ou cliente em uma `ApiException` padronizada, garantindo que o SDK sempre comunique falhas de forma consistente.

Isso remove a necessidade de ter blocos `try/catch` repetidos em cada `Action`, tornando o código mais limpo e seguro.

---

## 🧭 Regras de Implementação

- Toda `Action` deve **estender `AbstractAction`**.
- Toda `Action` deve **utilizar um DTO** como parâmetro de entrada para seu método principal.
- O método principal deve se chamar `handle()`.
- O método `handle()` deve **sempre retornar um `array`**, que é o resultado padronizado processado pelo `ResponseHandler`.

---

### ✅ Exemplo

```php
namespace AsaasPhpSdk\Actions\Customers;

use AsaasPhpSdk\Actions\AbstractAction;
use AsaasPhpSdk\DTOs\Customers\CreateCustomerDTO;

class CreateCustomerAction extends AbstractAction
{
    /**
     * @param CreateCustomerDTO $data O DTO com os dados do cliente validados.
     * @return array O array de resposta da API, processado pelo ResponseHandler.
     */
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
