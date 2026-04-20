# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado no [Keep a Changelog](https://keepachangelog.com/pt-br/1.1.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.5.0] - 2026-04-20

### Adicionado
- **Visibilidade de Rankings por Perfil**: Implementação de lógica no `RankingController` para permitir que Admins visualizem rankings não lançados.
- **Gestão de Rankings no Frontend**: 
    - Tag "Não Lançado" e botão "Lançar Ranking" na `RankingsList.vue` para Admins.
    - Alerta de ranking não lançado na `RankingDetail.vue`.
    - Bloqueio de acesso a rankings não lançados para usuários comuns (Erro 403).
- **Testes de Visibilidade**: Criação de `RankingVisibilityTest.php` para validar as permissões de acesso aos rankings.

## [0.4.0] - 2026-04-20

### Adicionado
- **Regras de Negócio de Palco e Avaliação**:
    - Restrição de acesso à tela de avaliação para jurados quando o concurso estiver finalizado (403).
    - Validação obrigatória de check-in para envio de competidores ao palco.
    - Sincronização em tempo real aprimorada com `jurorId` no evento `NotaAtribuida` para feedback visual no Admin.
- **Interface do Jurado (Aprimoramentos)**:
    - Substituição do controle deslizante por input numérico com suporte a decimais (0.1).
    - Atualização automática da tela ao mudar a apresentação via WebSockets.
    - Mensagem de estado de espera padronizada: "Aguarde o administrador liberar a avaliação".
- **Interface do Admin (Aprimoramentos)**:
    - Botão de "CHAMAR" desabilitado para competidores sem check-in.
    - Indicador visual de status de check-in na lista de apresentações.

### Fix
- **Broadcasting em Testes**: Implementação de mocks de eventos nos testes de integração para evitar erros de conexão com o servidor Reverb.
- **Consistência de Dados**: Garantia de que apresentações só entram no palco se o competidor estiver presente (check-in realizado).

## [0.3.0] - 2026-04-19

### Adicionado
- **Módulo de Ranking (Task 6)**: Implementação do ranking em tempo real para Admin e ranking final para o público após encerramento do concurso.
- **RankingController**: Novo controller dedicado para separar a lógica de resultados do CRUD do concurso.
- **Helpers de Sistema**: Criação de `FormatHelper` e registro via `HelperServiceProvider` para centralizar lógicas de formatação de notas e strings.
- **Integração Frontend de Ranking**: Adição de abas de ranking na tela de palco do Admin e exibição do pódio na tela pública.
- **Otimização N+1**: Revisão e aplicação de `eager loading` (`with()`) em todos os controllers de API para performance otimizada.

### Fix
- **Relacionamentos**: Adição do relacionamento faltante `scores` no model `Presentation`.
- **Testes de Ranking**: Criação de suíte de testes `RankingTest.php` cobrindo regras de acesso e cálculos de pontuação.

## [0.2.0] - 2026-04-19

### Adicionado
- **SPA com Vue 3**: Migração completa do frontend para Single Page Application utilizando Vue 3 (Composition API), Vue Router e Pinia.
- **API RESTful**: Implementação de controllers de API para todos os módulos (Auth, Eventos, Concursos, Jurados, Inscrições, Controle de Palco).
- **Real-time com Laravel Reverb**: Integração do Laravel Echo + Reverb na SPA Vue para sincronização instantânea do palco entre Admin, Jurados e Público.
- **Painel de Avaliação (Vue)**: Novo formulário de notas para jurados com suporte a inputs numéricos precisos e reset automático via WebSockets.
- **Controle de Palco (Vue)**: Painel administrativo para gestão de apresentações em tempo real com feed de atividade ao vivo e acompanhamento de votos.
- **Check-in via QR Code (Vue)**: Integração de scanner direto no navegador para credenciamento rápido de competidores.
- **Identidade Visual**: Implementação do Design System "Editorial Elétrico" com Tailwind CSS, Glassmorphism e Neons.

### Alterado
- **Arquitetura**: Transição de Livewire/Blade para SPA + API REST, desacoplando completamente o estado do frontend.
- **Autenticação**: Migração para Laravel Sanctum com suporte a tokens API.
- **Roteamento**: Centralização do fluxo de navegação no Vue Router com guards de proteção por papel.

## [0.1.5] - 2026-04-18

### Adicionado (Task 4 & 5 - Versão Original/Refinamentos)
- **Check-in e Credenciamento**: Geração automática de `qr_code_hash` (UUID) e visualização de QR Code para competidores aptos.
- **PontuacaoService**: Lógica central de avaliação com suporte a pesos e algoritmo de desempate por prioridade de critério.
- **Status de Apresentação**: Inclusão do status `FINALIZADA` para controle de ciclo de vida.
- **Evento NotaAtribuida**: Notificações WebSocket para atualizar o Admin sobre o progresso das avaliações.

### Alterado (Bugfix 1)
- **Filtros de Palco**: Listagem de candidatos agora exibe todos os aptos, mas permite envio ao palco apenas com check-in realizado.
- **Interface do Jurado**: Substituição de sliders por inputs numéricos para maior precisão técnica nas notas.
- **Estabilidade**: Melhoria no tratamento de fim de concurso para evitar carregamentos infinitos na tela do jurado.

### Fix
- Correção de casting de `checkin_realizado` para `boolean` no model `Presentation`.

## [0.1.0] - 2026-04-15

### Adicionado
- **Configuração Inicial**: Setup Laravel 12, Reverb e bibliotecas core.
- **Arquitetura de Banco de Dados**: Criação de todo o esquema relacional com suporte a `SoftDeletes`.
- **Autenticação e Segurança**: Middleware `CheckRole` e integração Sanctum.
- **Gestão de Eventos e Concursos**: CRUDs básicos para eventos, concursos e critérios de avaliação (Baremas).
- **Inscrições e Documentos (Task 3)**: Implementação de upload seguro e análise administrativa de documentos (MP3/Referências).
- **Service Layer**: Início da padronização de lógica em Services estáticos (`run()`).
- **Suíte de Testes Inicial**: Cobertura de Auth, CRUDs e Acesso por Perfil.
