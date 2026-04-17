## Sincronização em Tempo Real (Task 4)

### Added
- Configuração do **Laravel Reverb** para suporte a WebSockets e eventos em tempo real.
- Evento de broadcasting `ApresentacaoAlterada` disparado em canais públicos por concurso.
- Componente Livewire `StageViewer` para visualização pública do palco com atualização instantânea via Echo.
- Componente Livewire `EvaluationPanel` para jurados, com reset automático de formulário ao detectar nova apresentação no palco.
- Novas rotas: `/stage/{contest}` (público) e `/evaluate/{contest}` (jurado).
- Suíte de testes `RealTimeSyncTest` para validar o disparo de eventos e a renderização dos novos componentes.
- Factories para `Event` e `Contest` para suporte a testes automatizados.

### Changed
- Atualização do layout principal (`app.blade.php`) para incluir links de navegação para o perfil de Jurado.
- Inclusão do setup do Laravel Echo no bundle de ativos do frontend.
