# GEMINI.md - Guia de Desenvolvimento: Gestor de Apresentação Gemini

Este documento consolida as diretrizes, arquitetura e requisitos do projeto, servindo como a "Fonte da Verdade" para a implementação.

## 1. Visão Geral e Objetivo
Plataforma para gestão de concursos em eventos (K-Pop, Cosplay, etc.), substituindo planilhas por um sistema automatizado com controle de palco em tempo real, avaliação de jurados e ranking instantâneo.

### Perfis de Usuário
- **Admin:** Gestão total, definição de critérios, controle de palco e ranking exclusivo.
- **Jurado:** Avaliação em tempo real da apresentação ativa no palco.
- **Competidor:** Cadastro, envio de documentos, check-in via QR Code e acompanhamento.
- **Público:** Visualização da apresentação atual e ranking final.

---

## 2. Stack Tecnológica
- **Backend:** Laravel 12 (API RESTful)
- **Frontend:** Vue 3 (SPA) + Pinia + Vue Router + Tailwind CSS
- **Real-time:** Laravel Reverb (WebSockets)
- **Autenticação:** Laravel Sanctum
- **Banco de Dados:** MySQL 8.0
- **Bibliotecas Chave:** `simplesoftwareio/simple-qrcode`, `laravel/echo`, `pusher-js`

---

## 3. Documentação de Referência
Para informações detalhadas, consulte:
- **`architecture.md`**: Padrões de projeto, Service Layer e estrutura SPA.
- **`database.md`**: Esquema de tabelas, relacionamentos e SoftDeletes.
- **`features.md`**: Mapeamento de funcionalidades por perfil de usuário.
- **`frontend.md`**: Design System "Editorial Elétrico", cores e tipografia.
- **`prd.md`**: Visão de produto e objetivos do sistema.

---

## 4. Arquitetura e Padrões de Código
### Convenções Gerais
- **Idioma:** Nomes de variáveis, classes e tabelas **sempre em Inglês**.
- **DB:** Colunas em `snake_case`. Todos os models com `SoftDeletes`.
- **PSR:** Seguir padrões PSR-1, PSR-2 e PSR-12.
- **MVC + Service Layer:** Lógica isolada em `app/Services` com método estático de entrada.
- **API First:** Validação via `Form Request` em todas as entradas. Redirecionamento automático se o token Sanctum for inválido.
- **Gestão de Arquivos:** Uso de S3 com links temporários (5 min) para acesso privado.
- **Entidades Principais:** `users`, `roles`, `events`, `contests`, `presentations`, `presentation_documents`, `evaluation_criteria`, `presentation_scores`, `contest_jurors`.


## 5. Estratégia Real-Time (Laravel Reverb)
- **Canais:** Públicos e Privados via `routes/channels.php`.
- **Consumo:** Reatividade instantânea na SPA via `Laravel Echo`.
- **Eventos:** `ApresentacaoAlterada`, `NotaAtribuida`.

---

## 6. Design System: "Editorial Elétrico"
### Estética e Cores
- **Conceito:** "Palco Digital" com alto contraste e neons sobre fundo "Obsidiana".
- **Hierarquia de Superfícies:**
    - `surface`: `#0e0e13` (Base)
    - `surface-container-low`: `#131318` (Secundário)
    - `surface-container-high`: `#1f1f26` (Floating)
    - `surface-container-highest`: `#25252c` (Inputs)
- **Vidro e Gradiente:** *Glassmorphism* (`backdrop-filter: blur(20px)`) para elementos flutuantes. CTAs com gradiente de `primary` (#ed86ff) para `primary-container` (#e76eff) a 135°.
- **Regra de Ouro:** Proibido bordas de 1px sólidas. Use mudanças de cor ou "Bordas Fantasmas" (`outline-variant` #48474d com 20% opacidade).

### Componentes e Tipografia
- **Tipografia:** *Space Grotesk* (Displays/Rankings), *Manrope* (Corpo/Títulos), *Inter* (Dados Admin).
- **Badges:** APTO (Texto secondary #00eefc sobre 10% opacidade), INAPTO (Texto error #ff6e84 sobre 10% opacidade).

---

## 7. Funcionalidades Críticas
- **Check-in via QR Code:** Scanner integrado na SPA para credenciamento.
- **Controle de Palco:** Sincronização automática entre Admin, Jurados e Público.
- **Ranking Ponderado:** Cálculo instantâneo com desempate por prioridade.

---

## 8. Diretrizes para o Agente (Fluxo de Trabalho)
- **Contexto:** SEMPRE utilize os arquivos da pasta `docs/**` como contexto principal para qualquer implementação ou tomada de decisão arquitetural.
- **Testes:** É MANDATÓRIO criar testes automatizados (Pest ou PHPUnit) para cada nova funcionalidade ou regra de negócio implementada.
- **Changelog:** Ao finalizar cada tarefa do roadmap, o arquivo `CHANGELOG.md` deve ser atualizado em Português (PT-BR) seguindo o padrão [Keep a Changelog](https://keepachangelog.com/pt-br/1.1.0/).
- **Surgical Updates:** Realize modificações precisas e idiomáticas, respeitando as convenções estabelecidas.

---

## 9. Roadmap de Implementação
1. **Fundação:** Setup, Auth Sanctum, Roles, Users e SPA Base. [Concluído]
2. **Eventos & Baremas:** CRUDs de Eventos, Concursos e Critérios. [Concluído]
3. **Inscrições:** Gestão de Documentos (S3) e Apresentações. [Concluído]
4. **Real-Time:** Setup Reverb, Echo e Integração QR Code. [Concluído]
5. **Avaliação:** Sistema de notas (Barema) e Controle de Palco. [Concluído]
6. **Resultados:** Ranking Admin e Público, Visibilidade Controlada. [Concluído]
7. **Documentação:** Consolidação técnica e refatoração de guias. [Concluído]

---

*Este guia deve ser consultado antes de qualquer nova implementação para garantir consistência arquitetural e visual.*
