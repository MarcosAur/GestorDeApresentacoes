## Bugfix 1: Ajustes no Sistema de Concurso e Avaliação

### Added
- **Status FINALIZADA**: Adicionado novo status para apresentações via migration, permitindo o encerramento do ciclo de vida de cada candidato no palco.
- **Evento NotaAtribuida**: Novo evento WebSocket para atualizar o painel do administrador em tempo real sempre que um jurado envia uma nota.
- **Testes de Fluxo**: Suíte de testes `Bugfix1Test` cobrindo transições de status e regras de visibilidade de botões.

### Changed
- **Stage Controller (Admin)**: 
    - Listagem agora exibe todos os candidatos APTOS (mesmo sem check-in).
    - Botão "Mandar ao Palco" habilitado apenas para candidatos com check-in realizado.
    - Transição automática para status `FINALIZADA` ao trocar de candidato ou encerrar concurso.
    - Atualização reativa da lista de jurados que votaram via WebSocket.
- **Painel de Avaliação (Jurado)**:
    - Substituição do Slider por Input Numérico para maior precisão e facilidade de uso.
    - Tratamento de redirecionamento/feedback visual amigável quando o concurso é encerrado.
- **PontuacaoService**: Integração com o evento `NotaAtribuida`.
