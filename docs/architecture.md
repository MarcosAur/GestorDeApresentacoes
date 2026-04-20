# Arquitetura e Padrões de Projeto

Este documento detalha a arquitetura técnica e os padrões de desenvolvimento adotados na plataforma **Gestor de Apresentação Gemini**.

## 1. Stack Tecnológica

O sistema é construído sobre uma arquitetura desacoplada (Decoupled), separando completamente o motor de processamento (Backend) da interface de usuário (Frontend).

### Backend (API RESTful)
- **Framework:** Laravel 12 (PHP 8.2+)
- **Autenticação:** Laravel Sanctum (Stateful para SPA e Token-based para dispositivos).
- **Real-time:** Laravel Reverb (Servidor de WebSockets nativo).
- **Banco de Dados:** MySQL 8.0.
- **Cache/Queue:** Redis (opcional para performance em produção).

### Frontend (SPA)
- **Framework:** Vue 3 (Composition API).
- **Gerenciamento de Estado:** Pinia.
- **Roteamento:** Vue Router.
- **Estilização:** Tailwind CSS.
- **Client WebSockets:** Laravel Echo + Pusher-JS.

---

## 2. Padrões de Projeto (Backend)

### 2.1. Service Layer Pattern
Toda a lógica de negócio pesada é isolada em classes dentro de `app/Services`.
- **Convenção:** Uso de métodos estáticos semânticos como `run()` ou verbos específicos (ex: `PontuacaoService::getRanking()`).
- **Benefício:** Facilita testes unitários e reutilização de código entre Controllers e comandos CLI.

### 2.2. Form Requests
A validação de entrada de dados é delegada a classes específicas em `app/Http/Requests`.
- **Obrigatoriedade:** Nenhum Controller deve processar dados sem antes passarem por um Form Request validado.

### 2.3. Broadcasting (Real-time Events)
Utilizamos eventos do Laravel para notificar o frontend sobre mudanças de estado no palco.
- **Eventos Principais:** `ApresentacaoAlterada` (Troca de competidor no palco) e `NotaAtribuida` (Feedback instantâneo de avaliação).

### 2.4. Helpers
Lógicas de formatação compartilhadas residem em `app/Helpers` (ex: `FormatHelper`).

---

## 3. Padrões de Projeto (Frontend)

### 3.1. Arquitetura de Componentes
- **Base Components:** Localizados em `resources/js/components/`, são elementos atômicos reutilizáveis (Botões, Inputs, Modais).
- **Views/Pages:** Localizados em `resources/js/views/`, representam as telas completas e gerenciam o ciclo de vida dos dados.

### 3.2. Pinia Stores
O estado global (usuário autenticado, configurações de concurso, feeds de jurado) é gerenciado via Pinia em `resources/js/stores/`.

### 3.3. Axios Interceptors
Centralizamos a gestão de tokens e tratamento de erros 401/403 através de interceptors globais, garantindo que a expiração de sessão redirecione o usuário ao Login automaticamente.

---

## 4. Camadas de Segurança
1. **Sanctum Middleware:** Protege todos os endpoints `/api/`.
2. **Role Middleware:** Verifica se o `slug` do papel do usuário (Admin, Jurado, Competidor) permite o acesso ao recurso.
3. **Link Temporário (S3):** Documentos privados são servidos via URLs assinadas da Amazon S3 com validade de 5 minutos.
