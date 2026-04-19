## [x] Task 1: Fundação, Auth e Segurança (Concluída)

O objetivo é estabelecer a base sólida do sistema com as convenções MySQL e PSR.

  * **Setup Inicial:** Instalar Laravel 12 e instalar bibliotecas core (simple-qrcode , laravel-reverb ). [OK]
  * **Roles & Users:** Criar tabelas roles e users com relacionamento 1:N via role\_id. Ativar SoftDeletes em todos os models. [OK]
  * **Autenticação:** Configurar Laravel Sanctum para proteção de rotas de API. [OK]
  * **Middleware de Acesso:** Implementar middleware para validar permissões por slug da Role e o vínculo de jurados a concursos (jurado\_concurso). [OK]
  * **UI Base:** Layout principal com menu hambúrguer lateral esquerdo e informações do usuário no topo direito com logout. [OK]
  * **Tela de Autenticação:** Criação da tela de login para entrada na tela principal [OK]

## Task 2: Estrutura de Eventos e Baremas (Em Andamento)

Implementação da hierarquia de sub-eventos e critérios de avaliação customizados.

  * **CRUD de Eventos:** Criar events (listagem, adição via modal, edição e deleção protegida ).
  * **CRUD de Concursos:** Implementar contests vinculados a eventos com controle de status (AGENDADO, EM\_ANDAMENTO, FINALIZADO).
  * **Configuração de Critérios (Barema):** Criar evaluation\_criteria. Implementar lógica para nota\_maxima, peso e prioridade\_desempate.
  * **Vínculo de Jurados:** Tabela associativa contest\_jurors para definir quem avalia qual concurso.

## [x] Task 3: Inscrições e Gestão de Documentos (Concluída)

Foco na jornada do competidor e análise administrativa.

  * **Apresentações:** Criar tabela presentations com status inicial EM\_ANALISE. [OK]
  * **Gestão de Documentos:** Criar user\_documents linkada ao usuário. [OK]
      * **Frontend:** Tela exclusiva do competidor para upload e acompanhamento de status. (Bloquear deleção de documentos ). [OK]
  * **Análise Administrativa:** Tela para o Admin visualizar dados da apresentação + documentos e aprovar/reprovar com justificativa. [OK]
  * **Service Layer:** Criar DocumentService e PresentationService seguindo o padrão estático run(). [OK]

## [x] Task 4: Credenciamento e Real-Time (Core) (Concluída)

Implementação da dinâmica de palco e WebSockets.

  * **QR Code System:** Gerar qr\_code\_hash e disponibilizar visualização na tela do competidor. [OK]
  * **Check-in:** Funcionalidade de leitura para mudar status para checkin\_realizado. [OK]
  * **Setup Real-Time:** Configurar Laravel Reverb e disparar o evento ApresentacaoAlterada. [OK]
  * **Sincronização de Telas (Livewire):**
      * **Público:** Tela que re-renderiza ao detectar nova apresentacao\_atual\_id. [OK]
      * **Jurados:** Reset automático do formulário de notas ao mudar competidor no palco. [OK]

## [x] Task 5: Avaliação e Ranking Ponderado (Concluída)

Lógica complexa de notas e critérios de desempate.

  * **Lançamento de Notas:** Tela do jurado com formulário reativo baseado nos critérios do concurso. [OK]
  * **Validação de Notas:** Implementar Form Requests para garantir que a nota não exceda o limite definido no barema. [OK]
  * **PontuacaoService:**
      * Cálculo em tempo real de notas ponderadas (Soma de Nota × Peso). [OK]
      * Algoritmo de desempate por prioridade de critério. [OK]
  * **Controle de Palco:** Admin seleciona próxima apresentação e encerra a atual (Validando se todos os jurados votaram ). [OK]

## Task 6: Consolidação e Resultados

Finalização do ciclo de vida do concurso e entrega de dados.

  * **Ranking Admin:** Tela exclusiva para o Admin visualizar o ranking em tempo real durante o concurso.
  * **Ranking Geral:** Tela de resultados para o público liberada apenas após o status FINALIZADO.
  * **Otimização:** Revisar todas as queries para garantir que não haja $n+1$ (uso de with()).
  * **Helpers:** Mover lógicas de formatação de notas ou strings para app/helpers.

## [x] Bugfix 1: Ajustes e Correções no Sistema de Concurso e Avaliação (Concluída)

📋 Descrição Geral

Esta tarefa contempla uma série de ajustes na listagem de candidatos, interface de avaliação do jurado, correções de fluxo de status e resolução de problemas de reatividade via WebSocket.

🛠️ Detalhamento das Atividades

1. Gestão de Candidatos e Palco

[x] Exibição de Candidatos Aptos: - Alterar a query/filtro da listagem de palco para exibir todos os candidatos com status "Apto", independentemente de terem realizado o login no sistema. [OK]

[x] Trava de Botão "Enviar para o Palco": - Adicionar uma condicional na interface administrativa: o botão de ação para enviar o candidato ao palco só deve ficar habilitado/visível se o candidato estiver com a flag logged_in: true (checkin_realizado). [OK]

2. Interface do Jurado (UX/UI)

[x] Input de Nota: - Substituir o componente de Slider pelo componente de Campo de Texto (Input) na tela de atribuição de notas. [OK]

Garantir validação numérica (ex: 0 a 10) e suporte a casas decimais se necessário. [OK]

3. Reatividade e WebSockets (Sincronização)

[x] Correção do Real-time (Admin): - Investigar por que a tela de palco do administrador não está refletindo as mudanças de estado sem Refresh (F5). [OK - Implementado NotaAtribuida]

[x] Correção do Real-time (Jurado): - Investigar e corrigir a falha na atualização reativa da tela de avaliação do jurado. [OK]

Checklist: Verificar listeners de socket, emissão de eventos no backend e hooks de estado no frontend. [OK]

4. Fluxo de Status da Apresentação

[x] Finalização de Ciclo: - Implementar lógica para que, assim que uma apresentação for removida do palco, seu status seja automaticamente alterado para FINALIZADA. [OK]

[x] Trava de Retorno: - Impedir que apresentações com status FINALIZADA possam ser movidas de volta para o estado "Em Apresentação" ou que permitam novas avaliações. [OK]

5. Regras de Negócio e Encerramento

[x] Visibilidade do Botão "Encerrar": - O botão de "Encerrar Concurso" deve ser ocultado ou desabilitado caso o status do concurso já conste como ENCERRADO ou FINALIZADO. [OK]

[x] Tratamento de Concurso Finalizado (Jurado): - Corrigir o Infinite Loading na tela de avaliação quando o concurso já acabou. [OK]

Solução esperada: Redirecionar o jurado para uma tela de "Concurso Finalizado" ou exibir uma mensagem clara de que as avaliações estão encerradas. [OK]

📌 Critérios de Aceite

Candidatos offline aparecem na lista, mas não podem subir ao palco. [OK]

Jurados digitam a nota manualmente. [OK]

Mudanças no palco aparecem instantaneamente para Admin e Jurado sem F5. [OK]

Apresentações encerradas não permitem reentrada. [OK]

Jurado não fica preso em telas de carregamento após o fim do evento. [OK]
