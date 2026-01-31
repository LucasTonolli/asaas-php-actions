# ⚙️ Services

Os **Services** são a principal interface de interação com o SDK. Eles atuam como uma fachada (_Façade_) que agrupa todas as operações disponíveis para um recurso específico da API, como `Customer`, `Payment`, `Subscription`, etc.

O objetivo de um `Service` é fornecer uma **API pública, coesa e fácil de usar**, abstraindo do usuário final a complexidade e a existência das camadas internas de `DTOs` e `Actions`.

---

## 📌 Estrutura e Convenções

- **Padrão:** `{Recurso}Service` (ex: `CustomerService`, `PaymentService`)
- **Namespace:** `AsaasPhpSdk\Services`
- **Localização:** `src/Services/{Recurso}Service.php`

---

## 🏗️ A Classe Abstrata (`AbstractService`)

Para garantir consistência, todos os `Services` devem estender a classe `AbstractService`. Esta classe base é responsável por:

1.  **Injetar Dependências:** Recebe o `Client` HTTP e o `ResponseHandler` via construtor.
2.  **Centralizar a Criação de DTOs:** Fornece um método helper `createDTO()` que simplifica a criação de DTOs e padroniza o tratamento de erros de validação.

### O Helper `createDTO()`

Este método é um dos pilares da arquitetura dos `Services`.

```php
protected function createDTO(string $dtoClass, array $data): AbstractDTO
```

Sua função é chamar o método `fromArray()` do DTO e, crucialmente, **capturar qualquer `InvalidDataException`** (a exceção base para erros de validação de DTOs) e **envelopá-la em uma `ValidationException`**.

Isso é importante porque `InvalidDataException` é uma exceção interna do domínio dos DTOs. O `Service`, como camada pública, traduz esse erro para uma `ValidationException`, que é a exceção pública que o usuário do SDK deve esperar para erros de validação.

---

## 🧭 Princípios de Design

1.  **Agrupamento por Recurso:** Cada `Service` é responsável por gerenciar o ciclo de vida de um único recurso da API.

2.  **Interface Simplificada:** Os métodos do `Service` recebem e manipulam dados brutos, como `arrays` e `strings`. A responsabilidade de transformar esses dados em `DTOs` tipados é interna.

3.  **Delegação para Actions:** Um `Service` **não contém a lógica** para executar a chamada HTTP. Sua função é orquestrar o fluxo:
    - Receber os dados brutos do usuário.
    - Chamar `createDTO()` para obter uma instância do `DTO`.
    - Instanciar a `Action` correspondente, injetando as dependências.
    - Delegar a execução para o método `handle()` da `Action`.
    - Retornar o resultado.

---

### ✅ Exemplo - `CustomerService.php`

Este exemplo mostra como o `CustomerService` estende `AbstractService` e utiliza seus recursos.

```php
namespace AsaasPhpSdk\Services;

use AsaasPhpSdk\Actions\Customers\{CreateCustomerAction, GetCustomerAction};
use AsaasPhpSdk\DTOs\Customers\{CreateCustomerDTO};
use AsaasPhpSdk\Services\Base\AbstractService;

final class CustomerService extends AbstractService
{
    /**
     * Cria um novo cliente.
     *
     * @param array $data Dados do cliente.
     * @return array Dados do cliente criado.
     */
    public function create(array $data): array
    {
        // 1. Usa o helper da AbstractService para criar o DTO.
        // A lógica de try/catch e o wrapping da exceção já estão encapsulados.
        $dto = $this->createDTO(CreateCustomerDTO::class, $data);

        // 2. Instancia a Action específica para a operação
        $action = new CreateCustomerAction($this->transporter);

        // 3. Delega a execução para a Action e retorna o resultado
        return $action->handle($dto);
    }

    /**
     * Obtém um cliente pelo ID.
     *
     * @param string $id ID do cliente.
     * @return array Dados do cliente.
     */
    public function get(string $id): array
    {
        // Para operações simples que não usam DTO,
        // a Action é instanciada e chamada diretamente.
        $action = new GetCustomerAction($this->transporter);

        return $action->handle($id);
    }

    // ... outros métodos (list, update, delete, etc.)
}
```
