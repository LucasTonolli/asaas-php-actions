# Request Architecture Lifecycle

This document describes how the SDK handles outgoing requests and incoming responses, ensuring a decoupled, testable, and PSR-compliant architecture.

## Architecture Overview

The request flow follows a layered approach to isolate domain logic from infrastructure concerns.

### 1. The Service Layer (Entry Point)

Services (e.g., `CustomerService`) act as the public API. They are responsible for:

- Providing a fluent interface for the user.
- Instantiating and executing specific **Actions**.

### 2. Actions (`AbstractAction`)

Each API endpoint or use case is encapsulated in an **Action** class.

- **Responsability:** Knows the HTTP method, the endpoint path, and the required DTO.
- **Benefits:** High maintainability and single responsibility.

### 3. HttpTransporter (The Bridge)

The `HttpTransporter` is an internal component that bridges our domain with the HTTP protocol.

- **PSR-18 & PSR-17:** It works exclusively with PSR interfaces (`ClientInterface`, `RequestFactoryInterface`).
- **Data Transformation:** Converts DTO arrays into JSON streams.
- **Immutability:** Handles the immutable nature of PSR-7 messages.

### 4. ResponseHandler (The Gatekeeper)

Every response from the Asaas API passes through this handler.

- **Status Mapping:** Translates HTTP status codes (400, 401, 404, 429) into typed domain Exceptions.
- **Body Parsing:** Decodes JSON and extracts error messages in a human-readable format.

---

## Technical Stack & Decoupling

To ensure the SDK remains framework-agnostic, we use the following design patterns:

| Component            | Pattern                 | Implementation                                         |
| -------------------- | ----------------------- | ------------------------------------------------------ |
| **HTTP Client**      | Adapter (PSR-18)        | Can be Guzzle, Symfony, or any PSR-18 client.          |
| **Request Building** | Factory (PSR-17)        | Decouples the SDK from specific PSR-7 implementations. |
| **Client Creation**  | Static Factory Contract | Centralizes configuration (headers, retries, logs).    |
