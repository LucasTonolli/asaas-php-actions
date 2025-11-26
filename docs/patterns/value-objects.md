# 🗿 Value Objects (VO)

Os **Value Objects (VOs)** são a camada mais fundamental do domínio do SDK. Eles representam valores **imutáveis**, **autovalidados** e **autocontidos**, como `Cpf`, `Email` ou `CreditCard`.

Eles garantem que um valor, uma vez criado, esteja sempre em um estado válido. Toda a lógica de validação, formatação e comparação de um valor específico é centralizada dentro do seu respectivo VO.

---

## 📌 Tipos de Value Objects

A arquitetura do SDK define dois tipos de VOs, cada um com sua própria classe base abstrata:

### 1. `AbstractSimpleValueObject` (VOs Simples)

-   **Propósito:** Encapsular um único valor primitivo (geralmente uma `string`).
-   **Exemplos:** `Cpf`, `Email`, `Phone`.
-   **Factory:** Devem ser construídos usando um método estático `from(string $value)`.
-   **Funcionalidades herdadas:**
    -   `value()`: Retorna o valor primitivo encapsulado.
    -   `equals(self $other)`: Compara o valor com outro VO do mesmo tipo.

### 2. `AbstractStructuredValueObject` (VOs Estruturados)

-   **Propósito:** Representar valores complexos e compostos, que possuem múltiplas propriedades (que podem ser outros VOs).
-   **Exemplos:** `CreditCard`, `Discount`, `Split`.
-   **Factory:** Devem ser construídos usando um método estático `fromArray(array $data)`.
-   **Funcionalidades herdadas:**
    -   `toArray()`: Converte recursivamente o VO e seus filhos em um array.
    -   `equals(self $other)`: Compara o valor com outro VO comparando suas representações em array.

---

## 契約 `FormattableContract`

Esta interface pode ser implementada por qualquer VO (simples ou estruturado) que possua uma representação formatada para exibição.

-   **Método obrigatório:** `formatted(): string`
-   **Exemplo:** Um VO `Cpf` armazena o valor como `'12345678900'`, mas seu método `formatted()` retorna `'123.456.789-00'`.

---

## 🧱 Estrutura e Exemplos

### Exemplo 1: VO Simples (`Cpf.php`)

```php
// src/ValueObjects/Simple/Cpf.php

namespace AsaasPhpSdk\ValueObjects\Simple;

use AsaasPhpSdk\Exceptions\ValueObjects\Simple\InvalidCpfException;
use AsaasPhpSdk\Support\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Base\AbstractSimpleValueObject; // 1. Herda da base
use AsaasPhpSdk\ValueObjects\Contracts\FormattableContract;

// 2. É final e readonly para garantir imutabilidade
final readonly class Cpf extends AbstractSimpleValueObject implements FormattableContract
{
    // 3. Usa o método `from()` para construção
    public static function from(string $cpf): self
    {
        $sanitized = DataSanitizer::onlyDigits($cpf);

        if ($sanitized === null || strlen($sanitized) !== 11 || !self::isValidCpf($sanitized)) {
            throw new InvalidCpfException("Invalid CPF: {$cpf}");
        }

        // 4. O construtor é protegido e chamado apenas internamente
        return new self($sanitized);
    }

    // Lógica de validação específica do CPF
    public static function isValidCpf(string $cpf): bool
    {
        // ... implementação do algoritmo de validação
    }

    // 5. Implementação da FormattableContract
    public function formatted(): string
    {
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->value);
    }
}
```

### Exemplo 2: VO Estruturado (`Discount.php`)

```php
// src/ValueObjects/Structured/Discount.php

namespace AsaasPhpSdk\ValueObjects\Structured;

use AsaasPhpSdk\ValueObjects\Base\AbstractStructuredValueObject; // 1. Herda da base
use AsaasPhpSdk\ValueObjects\Structured\Enums\DiscountType;

// 2. É final e readonly
final readonly class Discount extends AbstractStructuredValueObject
{
    // 3. Construtor protegido com propriedades tipadas
    private function __construct(
        public float $value,
        public ?int $dueDateLimitDays,
        public DiscountType $discountType
    ) {}

    // 4. Usa o método `fromArray()` para construção
    public static function fromArray(array $data): self
    {
        $value = DataSanitizer::sanitizeFloat($data['value'] ?? null);
        // ... validações ...

        // Lógica de validação pode ser delegada para um factory privado
        return self::create(
            value: $value,
            // ...
        );
    }

    // 5. Lógica de negócio intrínseca ao valor
    public function calculateAmount(float $paymentValue): float
    {
        return match ($this->discountType) {
            DiscountType::Fixed => $this->value,
            DiscountType::Percentage => ($paymentValue * $this->value) / 100,
        };
    }
}
```

---

## 🧭 Boas Práticas

-   ✅ **Imutabilidade**: Use `readonly` e construtores `private` ou `protected` para garantir que um VO, uma vez criado, nunca mude.
-   ✅ **Validação no Factory**: Toda a lógica de validação deve ocorrer dentro dos métodos estáticos de construção (`from` ou `fromArray`). Um VO nunca deve ser instanciado em um estado inválido.
-   ✅ **Escolha a Base Correta**: Herde de `AbstractSimpleValueObject` para valores primitivos e de `AbstractStructuredValueObject` para valores compostos.
-   ✅ **Encapsule Lógica do Valor**: VOs devem conter lógica de negócio que seja **intrínseca ao valor que representam**. `Discount->calculateAmount()` é um bom exemplo. `Cpf->isValidCpf()` é outro.
-   ✅ **Use o `FormattableContract`**: Se precisar exibir o valor de forma "amigável", implemente esta interface. Não armazene o valor formatado internamente.
-   ✅ **Testes Unitários Dedicados**: Cada VO deve ter testes de unidade que cubram todos os cenários de validação (válidos e inválidos).
-   ❌ **Evite Dependências Externas**: Um VO não deve depender de serviços externos, repositórios ou da API. Ele deve ser autocontido.
