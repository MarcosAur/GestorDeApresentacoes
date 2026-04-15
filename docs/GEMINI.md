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
- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Blade Templates + Tailwind CSS + Livewire (Reatividade)
- **Real-time:** Laravel Reverb (WebSockets)
- **Autenticação:** Laravel Sanctum (SPA/Mobile)
- **Banco de Dados:** MySQL 8.0
- **Bibliotecas Chave:** `simplesoftwareio/simple-qrcode`

---

## 3. Arquitetura e Padrões de Código
### Convenções Gerais
- **Idioma:** Nomes de variáveis, classes e tabelas **sempre em Inglês**.
- **DB:** Colunas em `snake_case`. Todos os models com `SoftDeletes`.
- **PSR:** Seguir padrões PSR-1, PSR-2 e PSR-12.
- **MVC + Service Layer:** Lógica isolada em `app/Services` com método estático `run(...)`.
- **Helpers:** Lógicas compartilhadas em `app/helpers`.
### Seguranca: 
    - Validação via `Form Request` em todas as entradas (obrigatório para rotas e Livewire).
    - API REST com Bearer tokens; redirecionamento se token for inválido.
    - Middleware de acesso para validar permissões por slug de Role e vínculo jurado-concurso.
    - **Gestão de Arquivos:** Uso de S3 com links temporários (5 min) para acesso privado.


### Regras de Negócio e Banco de Dados
- **Deleção:** Registros só podem ser deletados se não possuírem dependências associadas.
- **Papéis (Roles):** Atribuição automática em telas de criação específicas (Competidor/Público) ou manual pelo Admin (Jurados/Admins).
- **Entidades:** `users`, `events`, `contests`, `presentations`, `evaluation_criteria`, `presentation_scores`, `roles`, `user_documents`, `contest_jurors` (N:M).

---

## 4. Estratégia Real-Time (Laravel Reverb)
- **Evento:** `ApresentacaoAlterada`.
- **Fluxo:** Disparado via Service quando `apresentacao_atual_id` em `contests` muda.
- **Consumo:** 
    - Público: Re-renderiza área do palco via Livewire no canal `concurso.{id}`.
    - Jurado: Reset automático do formulário de notas e carregamento do novo competidor.

---

## 5. Design System: "Editorial Elétrico"
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

## 6. Funcionalidades Críticas
- **Check-in via QR Code:** Competidor gera QR Code; Admin escaneia para marcar como "Presente".
- **Controle de Palco:** Admin seleciona próxima apresentação; validação de votos dos jurados antes de encerrar.
- **Ranking Ponderado:** Cálculo em tempo real (Nota × Peso) com critérios de desempate por prioridade.

---

## 7. Diretrizes para o Agente (Fluxo de Trabalho)
- **Contexto:** SEMPRE utilize os arquivos da pasta `docs/**` como contexto principal para qualquer implementação ou tomada de decisão arquitetural.
- **Testes:** É MANDATÓRIO criar testes automatizados (Pest ou PHPUnit) para cada nova funcionalidade ou regra de negócio implementada.
- **Changelog:** Ao finalizar cada tarefa do roadmap, o arquivo `CHANGELOG.md` deve ser atualizado em Português (PT-BR) seguindo o padrão [Keep a Changelog](https://keepachangelog.com/pt-br/1.1.0/).
- **Surgical Updates:** Realize modificações precisas e idiomáticas, respeitando as convenções estabelecidas.

---

## 8. Roadmap de Implementação
1. **Fundação:** Setup, Auth Sanctum, Roles, Users e UI Base (Menu hambúrguer).
2. **Eventos & Baremas:** CRUDs de Eventos, Concursos e Critérios (Baremas).
3. **Inscrições:** Apresentações e Gestão de Documentos (DocumentService/PresentationService).
4. **Real-Time:** Setup Reverb e integração QR Code/Check-in.
5. **Avaliação:** Sistema de notas (PontuacaoService) e Controle de Palco.
6. **Resultados:** Ranking Admin e Público, otimizações de query (evitar n+1).

---

*Este guia deve ser consultado antes de qualquer nova implementação para garantir consistência arquitetural e visual.*
