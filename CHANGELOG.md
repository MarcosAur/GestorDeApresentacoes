# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado no [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.2.0] - 2026-04-19

### Adicionado
- **SPA com Vue 3**: Migração completa do frontend para Single Page Application utilizando Vue 3 (Composition API), Vue Router e Pinia.
- **API RESTful**: Implementação de controllers de API para todos os módulos (Auth, Eventos, Concursos, Jurados, Inscrições, Controle de Palco).
- **Real-time com Laravel Reverb**: Integração do Laravel Echo + Reverb na SPA Vue para sincronização instantânea do palco entre Admin, Jurados e Público.
- **Painel de Avaliação (Vue)**: Novo formulário de notas para jurados com sliders interativos e reset automático via WebSockets.
- **Controle de Palco (Vue)**: Painel administrativo para gestão de apresentações em tempo real com feed de atividade ao vivo.
- **Check-in via QR Code (Vue)**: Integração da biblioteca `html5-qrcode` para scanner direto no navegador com fallback para entrada manual.
- **Identidade Visual**: Portabilidade fiel do Design System "Editorial Elétrico" para componentes Vue (SFC), mantendo neons e glassmorphism.

### Alterado
- **Arquitetura**: Transição de Livewire/Blade para SPA + API REST, desacoplando completamente o estado do frontend.
- **Autenticação**: Migração para Laravel Sanctum com suporte a cookies stateful para SPA.
- **Roteamento**: Centralização do fluxo de navegação no Vue Router com guards de proteção por papel (Admin/Jurado/Competidor).

## [0.1.0] - 2026-04-15

### Adicionado
- **Configuração Inicial**: Inicializado projeto Laravel 12 com `simplesoftwareio/simple-qrcode`, `laravel/reverb` e `livewire/livewire`.
- **Arquitetura de Banco de Dados**:
  - Tabelas principais: `roles`, `users`, `events`, `contests`, `evaluation_criteria`, `presentations`, `presentation_scores` e `user_documents`.
  - Ativação de `SoftDeletes` em todas as entidades.
  - Relacionamento de jurados com concursos (N:M).
- **Autenticação e Segurança**:
  - `AuthController` com login/logout e proteção Laravel Sanctum.
  - Middleware `CheckRole` para controle de acesso por slug e vínculo específico de jurados.
  - Restrição de rotas administrativas para usuários com perfil `admin`.
- **Interface (Design System "Editorial Elétrico")**:
  - Tailwind CSS 4.0 com paleta obsidiana e acentos neon.
  - Layout com barra lateral retrátil (Alpine.js).
  - Componentes com *glassmorphism* e tabelas sem linhas.
- **Gestão de Eventos**:
  - CRUD reativo via Livewire com proteção contra deleção de eventos ativos.
- **Gestão de Concursos e Baremas**:
  - CRUD de concursos vinculado a eventos.
  - Editor de Critérios de Avaliação integrado com suporte a pesos e notas máximas.
  - Validação para impedir duplicidade em prioridades de desempate.
- **Gestão de Jurados**:
  - CRUD de jurados com atribuição automática de papéis.
  - Possibilidade de vincular jurados aos concursos tanto pela tela de Jurados quanto pela tela de Concursos.
- **Inscrições e Gestão de Documentos (Task 3)**:
  - **Models e Relacionamentos**: Configuração de `Presentation` e `UserDocument` com suporte a `SoftDeletes` e vínculos com `User` e `Contest`.
  - **Service Layer**: Implementação de `PresentationService` e `DocumentService` seguindo o padrão estático `run()`.
  - **Validação Robusta**: Criação de Form Requests (`StorePresentationRequest`, `StoreDocumentRequest`, `EvaluatePresentationRequest`) para toda entrada de dados.
  - **Área do Competidor**: Painel Livewire (`EnrollmentPanel`) para inscrição em concursos e upload seguro de documentos.
  - **Análise Administrativa**: Painel Livewire (`PresentationAnalyzer`) para análise de apresentações e documentos, com sistema de aprovação/reprovação (justificativa obrigatória).
  - **Segurança**: Rota de download de documentos protegida, garantindo acesso apenas ao dono ou administradores.
  - **Testes Automatizados**: Inclusão de `PresentationServiceTest` e `DocumentServiceTest` para validar lógica de negócio e integridade de arquivos.
- **Seeders**:
  - `RoleSeeder` para papéis padrão.
  - `UserSeeder` para geração de usuários de teste com credenciais aleatórias.
- **Testes Automatizados**:
  - `AuthenticationTest`: Fluxo de login e logout.
  - `RoleAccessTest`: Permissões de acesso por perfil.
  - `EventCrudTest`: Integridade do CRUD de eventos.
  - `ContestCrudTest`: Validação de concursos e critérios.
  - `JurorCrudTest`: Gestão e vínculo de jurados.

### Alterado
- Refatoração da tabela `users` para remover `user_type`, centralizando permissões no `role_id`.
- Ajuste de layout de `@yield` para `{{ $slot }}` para suporte total a componentes Livewire de página inteira.
