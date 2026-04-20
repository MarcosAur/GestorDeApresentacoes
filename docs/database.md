# Estrutura do Banco de Dados

Este documento descreve as tabelas, relacionamentos e regras de persistência da plataforma.

## 1. Visão Geral
- **Motor:** MySQL 8.0
- **Soft Deletes:** Implementado em todas as entidades principais para permitir recuperação de dados.
- **Convenções:** Nomes de tabelas e colunas em Inglês (`snake_case`).

---

## 2. Entidades Principais

### 2.1. Usuários e Permissões
- **`roles`**: Papéis do sistema (`admin`, `jurado`, `competidor`, `publico`).
- **`users`**: Cadastro de usuários vinculado a um `role_id`.

### 2.2. Eventos e Concursos
- **`events`**: Eventos principais (ex: "Anime Expo 2026").
- **`contests`**: Concursos dentro de um evento (ex: "K-Pop Solo").
    - Coluna `ranking_released`: Controla se o ranking está visível para o público.
    - Coluna `status`: `AGENDADO`, `EM_ANDAMENTO`, `FINALIZADO`.

### 2.3. Apresentações e Documentos
- **`presentations`**: Inscrição de um competidor em um concurso.
    - Coluna `status`: `EM_ANALISE`, `APTO`, `INAPTO`, `EM_APRESENTACAO`, `FINALIZADA`.
    - Coluna `qr_code_hash`: UUID para check-in.
- **`presentation_documents`**: Arquivos enviados pelos competidores (MP3, Referências). Armazena o caminho relativo no S3.

### 2.4. Avaliação
- **`evaluation_criteria`**: Quesitos de nota (Barema). Possui `max_score`, `weight` e `tie_break_priority`.
- **`presentation_scores`**: Notas atribuídas pelos jurados para cada critério.
- **`contest_jurors`**: Tabela associativa (N:M) que vincula jurados aos concursos que eles podem avaliar.

---

## 3. Regras de Integridade
- **Deleção:** Registros com dependências associadas (ex: Concurso com Inscrições) não podem ser excluídos permanentemente através da interface.
- **SoftDeletes:** O campo `deleted_at` é usado em vez de `DELETE` físico para manter a rastreabilidade histórica.
- **IDs:** Uso de `BigIncrements` como chave primária padrão do Laravel.
