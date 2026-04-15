# Design System: Plataforma de Gestão de Concursos

## 1\. Stack Tecnológica

  * **Framework:** Laravel 12
  * **Frontend:** Blade Templates + Tailwind CSS
  * **Real-time:** Laravel Reverb (WebSockets) + Livewire (para reatividade no Blade)
  * **Autenticação:** Laravel Sanctum (SPA/Mobile compatibility)
  * **Banco de Dados:** MySQL 8.0
  * **Arquitetura:** MVC + Service Layer + Princípios SOLID

-----

## 2\. Arquitetura de Banco de Dados (ERD)

### 2.1. Tabela: `users`

*Padrão Laravel com diferenciação de papel.*

  * `id` (PK)
  * `role_id` (FK -\> `roles.id`)
  * `name`, `email`, `password`
  * `tipo_usuario` (Enum: 'ADMIN', 'JURADO', 'COMPETIDOR')
  * `timestamps`

### 2.2. Tabela: `eventos`

  * `id` (PK)
  * `admin_id` (FK -\> `users.id`)
  * `nome` (Varchar)
  * `data_evento` (Date)
  * `descricao` (Text)

### 2.3. Tabela: `concursos`

  * `id` (PK)
  * `evento_id` (FK -\> `eventos.id`)
  * `nome` (Varchar)
  * `status` (Enum: 'AGENDADO', 'EM\_ANDAMENTO', 'FINALIZADO')
  * `apresentacao_atual_id` (FK -\> `apresentacoes.id`, Nullable) - *Controla o Live.*

### 2.4. Tabela: `apresentacoes`

  * `id` (PK)
  * `concurso_id` (FK -\> `concursos.id`)
  * `competidor_id` (FK -\> `users.id`)
  * `titulo_trabalho` (Varchar)
  * `status_inscricao` (Enum: 'EM\_ANALISE', 'APTO', 'INAPTO')
  * `justificativa_inapto` (Text, Nullable)
  * `qr_code_hash` (Varchar, Unique)
  * `checkin_realizado` (Boolean, default: false)
  * `ordem_apresentacao` (Int)

### 2.5. Tabela: `jurado_concurso` (N:M)

  * `jurado_id` (FK -\> `users.id`)
  * `concurso_id` (FK -\> `concursos.id`)

### 2.6. Tabela: `criterios_avaliacao`

  * `id` (PK)
  * `concurso_id` (FK -\> `concursos.id`)
  * `nome_criterio` (Varchar)
  * `nota_maxima` (Decimal 5,2)
  * `peso` (Decimal 5,2, default: 1.0)
  * `prioridade_desempate` (Int, Nullable)

### 2.7. Tabela: `notas_apresentacoes`

  * `id` (PK)
  * `jurado_id` (FK -\> `users.id`)
  * `apresentacao_id` (FK -\> `apresentacoes.id`) - *Contexto da submissão.*
  * `criterio_id` (FK -\> `criterios_avaliacao.id`) - *Nota por critério.*
  * `nota_atribuida` (Decimal 5,2)

### 2.8. Tabela: `Roles`

  * `id` (PK)
  * `nome` (Varchar) – Ex: "Juiz de Prova"
  * `slug` (Varchar, Unique) – Ex: "jurado"
  * `timestamps`
  * `slug` (Varchar, Unique) – Ex: "jurado"

### 2.9. Tabela: `user_documents`

Esta tabela armazenará a referência aos arquivos físicos e o status de cada documento individual.

  * `id` (PK)
  * `user_id` (FK -\> `users.id`) — *Relacionamento 1:N (Um usuário pode enviar vários documentos, como RG, Termo de Autorização, etc).*
  * `type` (Varchar) — *Ex: 'identity\_card', 'parental\_consent', 'work\_preview'.*
  * `file_path` (Varchar) — *Caminho do arquivo no storage (Ex: documents/user\_1/rg.pdf).*
  * `timestamps`

-----

## 3\. Estratégia Real-Time

Para evitar o uso de *polling* (requisições repetitivas ao servidor), será implementada a seguinte estrutura:

  * **Motor:** **Laravel Reverb**. Um servidor de WebSocket nativo de alta performance.
  * **Evento:** `ApresentacaoAlterada`. Disparado via Service sempre que o `apresentacao_atual_id` na tabela `concursos` for atualizado.
  * **Consumo (Frontend):**
      * **Tela do Público:** Componente Livewire ouvindo o canal `concurso.{id}`. Ao receber o evento, o componente re-renderiza o Blade apenas na área do palco.
      * **Painel do Jurado:** Ao detectar a mudança, o formulário de notas do jurado é resetado e carregado com os dados do novo competidor automaticamente.

-----

## 4\. Camada de Negócio (Services)

  * A lógica será isolada em `app/Services` para manter os Controllers limpos (Single Responsibility):
  * Todas operações devem ter um service próprio com um método estatico de entrada `run` recebendo todos os parâmetros e usando injeção de dependencias

-----

## 5\. Endpoints & Segurança

  * **Sanctum:** Proteção de rotas administrativas e de jurados.
  * **Middleware de Acesso:** Garantir que um jurado só possa postar notas para concursos aos quais está vinculado na tabela `jurado_concurso`.
  * **Validação:** Uso de *Form Requests* para garantir que a `nota_atribuida` nunca exceda a `nota_maxima` do critério.

## 6\. Convenções gerais

  * Siga os padrões descritos nas PSR’s
  * Siga a arquitetura MVC
  * Todos os nomes usados em variaveis, classes e tabelas de banco devem ser em inglês
  * Nomes de colunas no banco devem seguir o modelo mysql (minusculo separado por underscores)
  * Lógicas compartilhadas devem ser divididas em helpers na pasta `app/helpers`
  * Para o qr code deve ser usado a biblioteca `simplesoftwareio/simple-qrcode` instalada via composer
  * Devem ser utilizadas rotas de api para conexão com o front
  * Todas as rotas devem ter suas entradas validadas via classes `request` do próprio framework laravel.
  * As consultas não devem gerar query n+1
  * As telas de crud devem ter uma listagem, um botão de adicionar. Este botão deve abrir um modal com os campos necessários para o cadastro
  * O front deve validar todos os dados antes de mandar para a API de acordo com o tipo de dado
  * Deve haver um menu hambúrguer na lateral esquerda que abre e fecha o dashboard
  * Deve haver as informações do usuário (nome) sem avatar na lateral superior direita. Deve ser clicável e ter a opção de logout.
  * Cada operação deve ter sua rota específica
  * A operação de deleção deve permitir deletar apenas registro que não possuam outros registros associados
  * Todos os models devem ter *softdeletes*
  * Todas as rotas devem ser autenticadas, menos a de login.
  * Todas as telas só podem ser vistas por um usuário logado
  * A API deve ser criada usando padrão REST com Bearer token
  * Caso o usuário não esteja logado ou com token vencido deve ser redirecionado para a tela inicial.
  * Cada usuário só pode editar informações básicas dele mesmo como nome, email
  * A role deve ser inserida automaticamente pela tela de criação específica (criação de usuário competidor ou publico) ou na hora do cadastro apenas pelo admin (no caso de jurados e admins)
  * Deve ter uma tela de recuperação de senha.

## 7\. Telas

  * Telas de crud genéricas para cada model com listagem, botão adicionar que abre um modal, botão editar que abre um modal preenchido e botão deletar
  * Página de cadastro de apresentação visível apenas para os usuários competidores
  * Detalhamento de apresentação: Visível apenas para o usuário competidor. É onde ele pode ver o status da apresentação (se foi aceita ou rejeitada e o motivo), enviar os documentos e gerar o qr code. Não deve haver possibilidade de deleção de documento.
  * Página de análise (aceita ou rejeita) de apresentações visível apenas para o usuário admin do evento exibindo as apresentações cadastradas com um botão (Deve conseguir visualizar todos os dados da apresentação incluindo documentos)
  * Tela de concurso: deve permitir todas as operações de visualizar e passar para a próxima apresentação, visualizar o ranking em tempo real e deve ser visivel apenas para o admin
  * Tela de notas: Esta deve ser visivel apenas para os jurados. Eles devem poder ver qual a apresentação atual e ter um formulário com os critérios para aquele concurso e podem avaliar de acordo com a magnitude definida para o critério
  * Tela de visualização: Deve ter uma tela visivel apenas para o usuário do tipo público com as informações da apresentação atual. Nela ele deve conseguir ver uma listagem de eventos em andamento e dos concursos deste evento
