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

## Task 3: Inscrições e Gestão de Documentos

Foco na jornada do competidor e análise administrativa.

  * **Apresentações:** Criar tabela presentations com status inicial EM\_ANALISE.
  * **Gestão de Documentos:** Criar user\_documents linkada ao usuário.
      * **Frontend:** Tela exclusiva do competidor para upload e acompanhamento de status. (Bloquear deleção de documentos ).
  * **Análise Administrativa:** Tela para o Admin visualizar dados da apresentação + documentos e aprovar/reprovar com justificativa.
  * **Service Layer:** Criar DocumentService e PresentationService seguindo o padrão estático run().

## Task 4: Credenciamento e Real-Time (Core)

Implementação da dinâmica de palco e WebSockets.

  * **QR Code System:** Gerar qr\_code\_hash e disponibilizar visualização na tela do competidor.
  * **Check-in:** Funcionalidade de leitura para mudar status para checkin\_realizado.
  * **Setup Real-Time:** Configurar Laravel Reverb e disparar o evento ApresentacaoAlterada.
  * **Sincronização de Telas (Livewire):**
      * **Público:** Tela que re-renderiza ao detectar nova apresentacao\_atual\_id.
      * **Jurados:** Reset automático do formulário de notas ao mudar competidor no palco.

## Task 5: Avaliação e Ranking Ponderado

Lógica complexa de notas e critérios de desempate.

  * **Lançamento de Notas:** Tela do jurado com formulário reativo baseado nos critérios do concurso.
  * **Validação de Notas:** Implementar Form Requests para garantir que a nota não exceda o limite definido no barema.
  * **PontuacaoService:**
      * Cálculo em tempo real de notas ponderadas (Soma de Nota × Peso).
      * Algoritmo de desempate por prioridade de critério.
  * **Controle de Palco:** Admin seleciona próxima apresentação e encerra a atual (Validando se todos os jurados votaram ).

## Task 6: Consolidação e Resultados

Finalização do ciclo de vida do concurso e entrega de dados.

  * **Ranking Admin:** Tela exclusiva para o Admin visualizar o ranking em tempo real durante o concurso.
  * **Ranking Geral:** Tela de resultados para o público liberada apenas após o status FINALIZADO.
  * **Otimização:** Revisar todas as queries para garantir que não haja $n+1$ (uso de with()).
  * **Helpers:** Mover lógicas de formatação de notas ou strings para app/helpers.
