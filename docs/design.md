# Design System: Plataforma de Gestão de Concursos

## 1. Stack Tecnológica

  * **Framework:** Laravel 12
  * **Frontend:** Blade Templates + Tailwind CSS
  * **Real-time:** Laravel Reverb (WebSockets) + Livewire (para reatividade no Blade)
  * **Autenticação:** Laravel Sanctum (SPA/Mobile compatibility)
  * **Banco de Dados:** MySQL 8.0
  * **Armazenamento:** Amazon S3 (via Flysystem) para documentos e mídias.
  * **Arquitetura:** MVC + Service Layer + Princípios SOLID

-----

## 2. Arquitetura de Banco de Dados (ERD)

### 2.1. Tabela: `users`

*Padrão Laravel com diferenciação de papel via Roles.*

  * `id` (PK)
  * `role_id` (FK -> `roles.id`)
  * `name`, `email`, `password`
  * `timestamps`, `soft_deletes`

### 2.2. Tabela: `events`

  * `id` (PK)
  * `admin_id` (FK -> `users.id`)
  * `name` (Varchar)
  * `event_date` (Date)
  * `description` (Text)
  * `timestamps`, `soft_deletes`

### 2.3. Tabela: `contests`

  * `id` (PK)
  * `event_id` (FK -> `events.id`)
  * `name` (Varchar)
  * `status` (Enum: 'AGENDADO', 'EM_ANDAMENTO', 'FINALIZADO')
  * `current_presentation_id` (FK -> `presentations.id`, Nullable) - *Controla o Live.*
  * `timestamps`, `soft_deletes`

### 2.4. Tabela: `presentations`

  * `id` (PK)
  * `contest_id` (FK -> `contests.id`)
  * `competitor_id` (FK -> `users.id`)
  * `work_title` (Varchar)
  * `status` (Enum: 'EM_ANALISE', 'APTO', 'INAPTO')
  * `justification_inapto` (Text, Nullable)
  * `qr_code_hash` (Varchar, Unique)
  * `checkin_realizado` (Boolean, default: false)
  * `presentation_order` (Int)
  * `timestamps`, `soft_deletes`

### 2.5. Tabela: `contest_jurors` (N:M)

  * `user_id` (FK -> `users.id`)
  * `contest_id` (FK -> `contests.id`)

### 2.6. Tabela: `evaluation_criteria`

  * `id` (PK)
  * `contest_id` (FK -> `contests.id`)
  * `name` (Varchar)
  * `max_score` (Decimal 5,2)
  * `weight` (Decimal 5,2, default: 1.0)
  * `tie_break_priority` (Int, Nullable)

### 2.7. Tabela: `presentation_scores`

  * `id` (PK)
  * `juror_id` (FK -> `users.id`)
  * `presentation_id` (FK -> `presentations.id`)
  * `criterion_id` (FK -> `evaluation_criteria.id`)
  * `score` (Decimal 5,2)

### 2.8. Tabela: `roles`

  * `id` (PK)
  * `name` (Varchar)
  * `slug` (Varchar, Unique)
  * `timestamps`

### 2.9. Tabela: `user_documents`

  * `id` (PK)
  * `user_id` (FK -> `users.id`)
  * `type` (Varchar) — *Ex: 'Documento de Identidade', 'Termo de Uso'.*
  * `file_path` (Varchar) — *Caminho no bucket S3.*
  * `timestamps`, `soft_deletes`

-----

## 3. Estratégia Real-Time

Para evitar o uso de *polling*, será implementada a seguinte estrutura:

  * **Motor:** **Laravel Reverb**.
  * **Evento:** `ApresentacaoAlterada`. Disparado via Service sempre que o `current_presentation_id` na tabela `contests` mudar.
  * **Consumo (Frontend):**
      * **Tela do Público:** Componente Livewire ouvindo o canal `concurso.{id}` para re-renderização parcial.
      * **Painel do Jurado:** Reset automático do formulário de notas e carregamento do novo competidor.

-----

## 4. Camada de Negócio (Services)

  * A lógica é isolada em `app/Services`.
  * **Padrão:** Cada operação deve ter um service próprio com um método estático de entrada `run(...)` (ou métodos semânticos como `evaluate()`) recebendo dados validados.

-----

## 5. Endpoints & Segurança

  * **Sanctum:** Proteção de rotas e persistência de sessão.
  * **Middleware de Acesso:** Validação de Roles e vínculos Jurado/Concurso.
  * **Gestão de Arquivos:** Uso obrigatório da facade `Storage` com o disco `s3`. Documentos privados são acessados exclusivamente via **Links Temporários Assinados** (validade de 5 minutos).
  * **Validação:** Uso obrigatório de *Form Requests* para todas as rotas e submissões Livewire.

## 6. Convenções gerais

  * Nomes em **Inglês** para variáveis, classes, colunas e tabelas.
  * Colunas em `snake_case`.
  * **SoftDeletes** em todos os Models.
  * Proibido query N+1 (utilizar `with()` sempre que necessário).
  * **Upload de Arquivos:** Limite de 5MB. Formatos: PDF, PNG, JPG, JPEG.
  * **Imutabilidade:** Competidores não podem deletar documentos após o envio.
  * **Design System "Editorial Elétrico":** Fundo obsidiana, glassmorphism, ausência de bordas sólidas de 1px (usar mudanças de cor ou outline-variant com baixa opacidade).

## 7. Telas Principais

  * **Dashboard:** Menu hambúrguer à esquerda, informações de usuário à direita superior.
  * **Painel do Competidor (`EnrollmentPanel`):** Inscrição em concursos, gestão de documentos (upload/visualização) e acompanhamento de status.
  * **Análise Admin (`PresentationAnalyzer`):** Visualização de dados e documentos do competidor. Aprovação (`APTO`) ou Reprovação (`INAPTO`) — justificativa obrigatória para reprovação.
  * **Controle de Palco:** Seleção de próxima apresentação e ranking em tempo real (Admin).
  * **Avaliação (Jurado):** Formulário reativo baseado nos critérios do concurso ativo.
