## Avaliação e Ranking Ponderado (Task 5)

### Added
- **PontuacaoService**: Implementação da lógica central de avaliação, validação de palco e algoritmo de ranking ponderado com suporte a prioridade de desempate.
- **StageController**: Novo painel administrativo para controle manual do palco, permitindo selecionar qual competidor sobe ao palco e validando votos pendentes antes de prosseguir.
- **EvaluationPanel**: Painel do jurado funcional com validação dinâmica baseada no barema (nota máxima por critério) e persistência no banco de dados.
- **Ponto de Entrada Único**: Acesso ao palco e avaliação centralizados na tela de Concursos (`ContestManager`).
- **Testes Automatizados**: Inclusão de `EvaluationFlowTest` e correções em `RealTimeSyncTest` e `CheckinTest` para garantir 100% de cobertura e sucesso.
- **Factories**: Criação da `PresentationFactory` para suporte a testes.

### Changed
- **ContestManager**: Readequação do componente e da rota para acesso compartilhado entre Admin e Jurados, com filtros de visibilidade por papel.
- **Sidebar**: Remoção de links estáticos de avaliação em favor do fluxo centralizado via lista de concursos.
- **Models**: Adição de `$fillable` e relacionamentos em `PresentationScore` para suporte a atribuição em massa.
