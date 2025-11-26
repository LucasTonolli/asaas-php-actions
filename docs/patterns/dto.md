# 📨 Data Transfer Objects (DTO)

Os **DTOs (Data Transfer Objects)** são responsáveis por transportar dados entre as camadas da aplicação de forma **estruturada**, **tipada** e **imutável**. Eles **não** contêm lógica de negócio, servindo como “contratos de dados” entre camadas.

A principal filosofia é: **dados que entram são validados rigorosamente; dados que saem são previsíveis e seguros.**

---

## 📌 Princípios Fundamentais

Com base no seu uso, podemos identificar dois tipos principais de DTOs:

1.  **DTOs de Mutação (Strict)**: Usados para **criar** ou **atualizar** recursos (ex: `CreateCustomerDTO`). São rigorosos: dados inválidos ou ausentes devem lançar exceções específicas para garantir a integridade total dos dados.
2.  **DTOs de Consulta (Lenient)**: Usados para **filtrar** ou **listar** recursos (ex: `ListCustomersDTO`). São mais permissivos: dados inválidos ou ausentes são convertidos para `null` ou ignorados, permitindo buscas flexíveis sem interromper o fluxo.

---

## 🧠 O Ciclo de Vida do DTO

Um DTO robusto segue um ciclo de vida claro, orquestrado pelo método estático `fromArray`.

| Método                     | Responsabilidade                                                                                                                                                                                                |
| :------------------------- | :-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `fromArray(array $data)`   | **Ponto de entrada público e final**. Orquestra o fluxo `Sanitize -> Validate -> Instantiate`.                                                                                                                      |
| `sanitize(array $data)`    | **(Protegido e Abstrato)** Primeira etapa. **Prepara e normaliza** os dados de entrada (ex: remove caracteres, ajusta tipos) antes da validação. Não lança exceções.                                             |
| `validate(array $data)`    | **(Protegido e Opcional)** Segunda etapa. **Valida as regras** e a integridade dos dados já sanitizados, **lançando exceções** em caso de falha. É aqui que `Value Objects` são instanciados.                       |
| `__construct(...)`         | **(Protegido)** Terceira etapa. Recebe os dados validados e os atribui às propriedades `readonly`, garantindo a imutabilidade do objeto.                                                                        |
| `toArray(): array`         | Converte o DTO em um array limpo, pronto para ser usado como payload de API.                                                                                                                                    |

---

## 🧱 Estrutura e Arquitetura

### 🧾 **Convenções**

- **Namespace**: `AsaasPhpSdk\DTOs`
- **Localização**: `src/DTOs/{Recurso}/{Verbo}{Recurso}DTO.php`
- **Nomeação**: **PascalCase**, indicando a ação (ex: `CreateCustomerDTO`).

### 🛠️ **Arquitetura de Suporte**

Para garantir consistência, a estrutura de DTOs se apoia em componentes centrais:

#### **1. `DTOContract` (Interface)**

É o contrato que **garante a API pública** de todos os DTOs. Ao forçar a implementação dos métodos `fromArray()` e `toArray()`, ele assegura que qualquer DTO no sistema possa ser construído e serializado de forma previsível.

#### **2. `AbstractDTO` (Classe Abstrata)**

É a base que fornece a **lógica reutilizável** para a maioria dos DTOs. Suas principais responsabilidades são:

- **Imutabilidade**: A classe e suas propriedades são `readonly`.
- **Ciclo de Vida Forçado**: Implementa o método `fromArray` como `final`, garantindo que o fluxo `sanitize -> validate -> instantiate` seja sempre seguido.
- **Conversão Inteligente (`toArray`)**: Implementa um método `toArray()` genérico usando Reflection. Ele automaticamente converte as propriedades públicas do DTO em um array, tratando tipos complexos de forma inteligente:
    -   Converte `BackedEnum` para seu valor (ex: `'CREDIT_CARD'`).
    -   Converte `UnitEnum` para seu nome.
    -   Chama o método `value()` em `Value Objects` simples.
    -   Obedece ao atributo `#[SerializeAs]` para customizar a serialização (ver abaixo).
    -   Propriedades com valor `null` são omitidas do resultado.

- **Helpers de Validação de VOs**:
    -   `validateSimpleValueObject()`: Tenta instanciar um VO que usa `::from()`.
    -   `validateStructuredValueObject()`: Tenta instanciar um VO que usa `::fromArray()`.

- **Helpers de Sanitização**: Fornece uma série de métodos (`optionalString`, `optionalInteger`, `optionalOnlyDigits`, etc.) que simplificam a sanitização de dados opcionais.

- **Forçar Implementação (`abstract sanitize`)**: Declara o método `sanitize()` como abstrato, **obrigando** cada DTO filho a implementar suas próprias regras de normalização de dados.

---

## ✨ Customizando a Serialização com `#[SerializeAs]`

O atributo `#[SerializeAs]` permite controlar como uma propriedade é convertida para array pelo método `toArray()`.

**Parâmetros:**
- `key`: Define uma chave customizada no array de saída. Útil para queries complexas (ex: `dateCreated[ge]`).
- `method`: O nome do método a ser chamado no objeto da propriedade.
- `args`: Um array de argumentos para passar ao método.

### Exemplo de `#[SerializeAs]`

```php
final readonly class CreatePaymentDTO extends AbstractDTO
{
    // No toArray(), isso se tornará:
    // 'dueDate' => $this->dueDate->format('Y-m-d')
    #[SerializeAs(method: 'format', args: ['Y-m-d'])]
    public \DateTimeImmutable $dueDate;
}

final readonly class ListPaymentsDTO extends AbstractDTO
{
    // No toArray(), isso se tornará:
    // 'dateCreated[ge]' => $this->dateCreatedStart->format('Y-m-d')
    #[SerializeAs(key: 'dateCreated[ge]', method: 'format', args: ['Y-m-d'])]
    public ?\DateTimeImmutable $dateCreatedStart = null;
}
```

---

## ✍️ Exemplos de Implementação

### Exemplo 1: DTO de Mutação (Strict)

Usa os helpers de `AbstractDTO` para validar e construir o objeto, lançando exceções se os dados forem inválidos.

```php
// src/DTOs/Customers/CreateCustomerDTO.php
final readonly class CreateCustomerDTO extends AbstractDTO
{
    // Propriedades com property promotion
    protected function __construct(
        public string $name,
        public Cpf|Cnpj $cpfCnpj,
        // ...
    ) {}

    // Obrigatório pela classe abstrata
    protected static function sanitize(array $data): array
    {
        return [
            // Usa os helpers para simplificar
            'name' => DataSanitizer::sanitizeString($data['name'] ?? ''),
            'cpfCnpj' => $data['cpfCnpj'] ?? null,
            // ...
        ];
    }

    protected static function validate(array $data): array
    {
        if (empty($data['name'])) {
            throw InvalidCustomerDataException::missingField('name');
        }

        // Lógica de validação complexa
        try {
            $sanitized = DataSanitizer::onlyDigits($data['cpfCnpj'] ?? '');
            $data['cpfCnpj'] = match (strlen($sanitized)) {
                11 => Cpf::from($sanitized),
                14 => Cnpj::from($sanitized),
                default => throw new InvalidValueObjectException('CPF/CNPJ inválido'),
            };
        } catch (InvalidValueObjectException $e) {
            throw InvalidCustomerDataException::invalidFormat('cpfCnpj', $e->getMessage());
        }

        return $data;
    }
}
```

### Exemplo 2: DTO de Consulta/Filtro (Lenient)

Campos inválidos são silenciosamente convertidos para `null` para não quebrar a busca, mas regras importantes (como a ordem das datas) ainda são validadas.

```php
// src/DTOs/Payments/ListPaymentsDTO.php
final readonly class ListPaymentsDTO extends AbstractDTO
{
    // ...
    protected static function sanitize(array $data): array
    {
        return [
            'limit' => self::optionalInteger($data, 'limit'),
            'dateCreatedStart' => self::optionalDateTime($data, 'dateCreatedStart'),
            'dateCreatedEnd' => self::optionalDateTime($data, 'dateCreatedEnd'),
        ];
    }

    protected static function validate(array $data): array
    {
        // Validação estrita para regras que não podem ser ignoradas
        if (isset($data['dateCreatedStart'], $data['dateCreatedEnd']) && $data['dateCreatedStart'] > $data['dateCreatedEnd']) {
            throw new InvalidDateRangeException('A "dateCreatedStart" deve ser anterior à "dateCreatedEnd"');
        }

        return $data;
    }
}
```

---

## 🧭 Boas Práticas

- ✅ **Imutabilidade**: Use `readonly` e `protected constructor`. A criação deve ser feita exclusivamente via `fromArray`.
- ✅ **Uso de VOs**: Incorpore `Value Objects` para validação em nível de campo.
- ✅ **Exceções Específicas**: Em DTOs de mutação, lance exceções de domínio claras.
- ✅ **Atributos para Customização**: Use `#[SerializeAs]` para manter a lógica de serialização declarativa e limpa.
- ✅ **Responsabilidade Única**: O DTO valida **estrutura e formato**, não regras de negócio complexas.
- ❌ **Evite Setters**: Nunca permita a alteração de um DTO após sua criação.
- ❌ **Não exponha o `__construct`**: Mantenha o construtor protegido para forçar o uso do factory `fromArray`.
