# Asaas PHP SDK

[![PHP](https://img.shields.io/badge/php-8.1%2B-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-brightgreen)](LICENSE)

Um SDK PHP não oficial, moderno e fluente para interagir com a [API Asaas](https://docs.asaas.com/reference).

Este SDK é construído com foco em princípios de **arquitetura limpa**, **segurança de tipo** (type-safety) e uma excelente experiência para desenvolvedores, aproveitando os recursos modernos do PHP 8.1+.

> ⚠️ **Atualmente em desenvolvimento ativo. As APIs podem sofrer alterações antes de um lançamento estável da v1.0.0.**

---

## ✨ Principais Funcionalidades

- **API Fluida e Intuitiva**: Encadeie métodos de forma lógica para acessar recursos e realizar ações (ex: `$asaas->cliente()->criar(...)`).
- **Tratamento de Erros Robusto e Previsível**: Chega de adivinhações sobre o que deu errado. O Theol lança exceções específicas e tipadas para diferentes cenários de erro (`ValidationException`, `NotFoundException`, `RateLimitException`, etc.).
- **Retentativas Automáticas**: Resiliência integrada. Requisições que falham devido a problemas de rede ou erros temporários do servidor (`5xx`, `429`) são automaticamente retentadas com uma estratégia inteligente de `backoff`.
- **Estruturas de Dados Imutáveis e Seguras por Tipo**: Utiliza DTOs e Value Objects `readonly` para garantir a integridade dos dados e prevenir mutações acidentais de estado.
- **PHP 8.1+ Moderno**: Aproveita os recursos modernos do PHP, como `Enums`, propriedades `readonly` e atributos para uma base de código limpa e de fácil manutenção.

---

## 🛠️ Começando

### 1. Instalação via Composer

```bash
composer require lucas-tonolli/asaas-php-sdk

```

> **Configuração de Plugins do Composer** > Este SDK depende do `php-http/discovery` para localizar automaticamente as implementações PSR-17 e PSR-18 disponíveis no seu projeto. Como o Composer só respeita a configuração `allow-plugins` do pacote raiz, você deve permitir explicitamente o uso deste plugin no seu `composer.json` para evitar prompts interativos ou falhas em ambientes de CI:
>
> ```bash
> composer config allow-plugins.php-http/discovery true
>
> ```
>
> Ou adicione manualmente ao seu `composer.json`:
>
> ```json
> "config": {
>     "allow-plugins": {
>         "php-http/discovery": true
>     }
> }
>
> ```

### 2. Exemplos Rápidos

#### Criando um Cliente

```php
use AsaasPhpSdk\AsaasClient;
use AsaasPhpSdk\Config\AsaasConfig;

// Configura para o ambiente de sandbox
$config = new AsaasConfig(token: 'SUA_TOKEN_SANDBOX', isSandbox: true);

// Instancia o cliente principal
$asaas = new AsaasClient($config);

$novoCliente = $asaas->client()->create([
    'nome' => 'João Silva',
    'cpfCnpj' => '12345678901',
    'email' => 'joao@example.com',
]);

print_r($novoCliente);
```

#### Tratamento de Erros de Validação

O Toolkit facilita a captura de erros específicos.

```php
use AsaasPhpSdk\AsaasClient;
use AsaasPhpSdk\Config\AsaasConfig;
use AsaasPhpSdk\Exceptions\Api\ValidationException;

$config = new AsaasConfig(token: 'SUA_TOKEN_SANDBOX', isSandbox: true);
$asaas = new AsaasClient($config);

try {
    // Tenta criar um cliente com dados inválidos
    $asaas->client()->create(['nome' => 'Maria Silva']); // cpfCnpj está faltando
} catch (ValidationException $e) {
    echo "Falha na validação: " . $e->getMessage();
    // Saída: Falha na validação: O campo obrigatório 'cpfCnpj' está faltando.
}
```

---

## 🏛️ Visão Geral da Arquitetura

O SDK segue os princípios de arquitetura limpa, separando as preocupações em camadas distintas.

- **Services**: A API pública para um recurso (ex: `CustomerService`). Este é o seu principal ponto de entrada para interagir com o SDK.
- **Actions**: "Casos de uso" internos que executam uma única operação específica (ex: `CreateCustomerAction`). Eles orquestram a criação de DTOs e as chamadas à API.
- **DTOs (Data Transfer Objects)**: Objetos estruturados, validados e imutáveis que transportam dados entre as camadas. Eles garantem que os dados são válidos antes que uma chamada à API seja feita.
- **Value Objects**: Objetos auto-validáveis e imutáveis que representam um único valor de domínio (ex: `Cpf`, `Email`, `CreditCard`). Eles garantem a consistência dos dados no nível mais baixo.
- **Exceptions**: Uma rica hierarquia de exceções personalizadas e tipadas que permitem um tratamento de erros preciso.
- **Helpers**: Classes utilitárias sem estado que lidam com preocupações transversais, como sanitização de dados (`DataSanitizer`), configuração de clientes HTTP (`HttpClientFactory`) e tratamento de respostas (`ResponseHandler`).

---

## 📂 Estrutura do Projeto

```
src/
├── Actions/
│   ├── Base/
│   └── {Recurso}/
├── DTOs/
│   ├── Base/
│   └── {Recurso}/
├── Exceptions/
│   ├── Api/
│   └── DTOs/
│   └── ValueObjects/
├── Services/
│   └── Base/
├── Support/
│   ├── Helpers/
│   └── Traits/
├── ValueObjects/
│   ├── Base/
│   ├── Simple/
│   └── Structured/
├── AsaasClient.php
└── Config/

tests/
├── Unit/
└── Integration/

docs/
├── patterns/
└── workflow/
```

---

## ⚡ Fluxo de Desenvolvimento

- **Branching**: `feature/*`, `fix/*`, `docs/*`
- **Commits**: Siga o [Conventional Commits](https://www.conventionalcommits.org/)
- **Testes**: Testes de Unidade + Integração são obrigatórios para novas funcionalidades.
- **Documentação**: Atualize `/docs/patterns` para quaisquer novas convenções.

---

## 📖 Marcos Atuais

- **v0.1.0** → Módulo Cliente (CRUD + Testes + Documentos) ✅
- **v0.2.0** → Módulo Pagamento (DTOs, Actions, Testes, Documentos) ✅
- **v0.3.0** → Módulo Webhook (CRUD + Documentos) ⏳
- **v0.3.0** → Refatorar documentação e padrões ✅
- **v1.0.0** → Lançamento Estável ⏳

---

## 📝 Notas

- A cobertura da API é **parcial**; alguns endpoints ainda estão em implementação.
- DTOs e Value Objects são **imutáveis**. Sempre use seus métodos estáticos `from()` ou `fromArray()` para criar novas instâncias.
