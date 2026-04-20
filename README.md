# Gestor de Apresentação Gemini 🎭

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

Plataforma moderna e reativa para gestão de concursos em eventos (K-Pop, Cosplay, Talentos, etc.). O sistema substitui planilhas físicas e processos manuais por um controle de palco automatizado, avaliações em tempo real e geração de rankings instantâneos utilizando WebSockets.

---

## 🚀 Tecnologias e Arquitetura

- **Backend:** Laravel 12 (API RESTful)
- **Frontend:** Vue 3 (SPA) + Vue Router + Tailwind CSS
- **Tempo Real:** Laravel Reverb (WebSockets)
- **Autenticação:** Laravel Sanctum (Tokens via API)
- **Banco de Dados:** MySQL 8.0

---

## 📋 Requisitos do Sistema

- PHP >= 8.2
- Composer >= 2.x
- Node.js >= 20.x e NPM
- MySQL >= 8.0

---

## 🛠️ Instalação e Configuração

Siga os passos abaixo para rodar o projeto localmente:

1. **Clone o repositório:**
   ```bash
   git clone https://github.com/seu-usuario/GestorDeApresentacaoGemini.git
   cd GestorDeApresentacaoGemini
   ```

2. **Instale as dependências do PHP e Node:**
   ```bash
   composer install
   npm install
   ```

3. **Configuração de Ambiente:**
   Copie o arquivo `.env.example` para `.env` e configure o banco de dados.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Certifique-se de que as variáveis do banco de dados (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) estão corretas.*

4. **Migrações e Seeders (Dados iniciais de teste):**
   ```bash
   php artisan migrate --seed
   ```
   *O seeder irá criar os papéis (roles) padrão e alguns usuários de teste (Admin, Jurado, Competidor).*

5. **Iniciando os Servidores:**
   Para rodar o projeto completamente, você precisará de 3 terminais rodando em paralelo:

   *Terminal 1 (Backend - API):*
   ```bash
   php artisan serve
   ```

   *Terminal 2 (Frontend - Vite):*
   ```bash
   npm run dev
   ```

   *Terminal 3 (WebSockets - Reverb):*
   ```bash
   php artisan reverb:start
   ```

6. **Acesso ao Sistema:**
   Acesse a aplicação via navegador em: `http://localhost:8000` ou a porta configurada pelo Vite/Artisan.

---

## 📖 Documentação de Módulos e Perfis (Roles)

O sistema possui quatro perfis principais de acesso, cada um com responsabilidades e telas específicas.

### 👑 Administrador (Admin)
Gestor absoluto do evento e concursos. Responsável pelo fluxo do evento do início ao fim.

- **Gestão de Eventos e Concursos:** Cria, edita e remove eventos principais e seus respectivos concursos.
- **Baremas (Critérios de Avaliação):** Define os critérios de notas (ex: Figurino, Performance), estabelecendo notas máximas, pesos e prioridade para critérios de desempate automático.
- **Vínculo de Jurados:** Associa usuários com perfil de "Jurado" aos concursos específicos que eles irão avaliar.
- **Análise de Inscrições:** Avalia os documentos submetidos pelos competidores, aprovando-os (status `APTO`) ou reprovando-os (`INAPTO`) com justificativa.
- **Credenciamento (Check-in):** Utiliza a câmera/scanner para ler o QR Code gerado pelo competidor, confirmando sua presença no evento físico.
- **Controle de Palco (Stage Control):**
  - Envia competidores aptos e presentes para o palco.
  - Acompanha ao vivo (via Reverb) quem já enviou a nota.
  - Impede a troca de competidor até que todos os jurados tenham votado.
- **Ranking em Tempo Real:** Visualiza o ranking atualizado automaticamente a cada nota atribuída pelos jurados, aplicando pesos e regras de desempate.

### ⚖️ Jurado (Juror)
Especialista que avalia as apresentações ativas no palco.

- **Acesso Restrito:** Só visualiza os concursos aos quais foi previamente vinculado pelo Administrador.
- **Tela de Avaliação Reativa:**
  - Visualiza imediatamente qual competidor subiu ao palco.
  - Preenche o formulário de notas com base no barema definido para o concurso.
  - A interface possui validação para impedir notas acima do limite.
- **Sincronização Automática:** Quando o administrador altera a apresentação no palco, a tela do jurado atualiza automaticamente para o próximo candidato, zerando o formulário.

### 🎤 Competidor (Competitor)
Usuário que se cadastra no sistema para participar dos concursos.

- **Cadastro e Submissão:** Realiza inscrição, seleciona o concurso e anexa os documentos obrigatórios (ex: áudio MP3, referências visuais).
- **Acompanhamento de Status:** Acompanha o status de sua inscrição (`EM_ANALISE`, `APTO`, `INAPTO`).
- **QR Code de Acesso:** Assim que considerado `APTO`, recebe um QR Code exclusivo em seu painel. Este QR Code deverá ser apresentado no dia do evento para credenciamento e liberação de palco.

### 👥 Público (Public)
Usuários não autenticados (ou autenticados sem privilégios) que acompanham o andamento do evento.

- **Visão de Palco (Stage Viewer):** Tela altamente visual (estética K-Pop/Neon) que mostra em tempo real qual competidor está se apresentando.
- **Ranking Final:** Apenas após o Administrador marcar o concurso como `FINALIZADO`, a tela do público passa a exibir a classificação oficial e transparente do concurso.

---

## 📡 Sincronização em Tempo Real (Eventos)

A comunicação via WebSocket (Laravel Reverb + Laravel Echo) utiliza os seguintes eventos principais:

- `ApresentacaoAlterada`: Disparado quando o Admin troca quem está no palco. Atualiza instantaneamente a tela do Público e limpa a tela de avaliação dos Jurados.
- `NotaAtribuida`: Disparado quando um jurado confirma suas notas. Atualiza o painel de status do Administrador (indicando quem já votou) e recalcula o Ranking Admin em tempo real.

---

## 🛡️ Segurança

- Proteção de rotas via `Laravel Sanctum`.
- Middlewares customizados (`CheckRole`) que garantem que apenas usuários com `slug` correspondente acessem endpoints sensíveis.
- Lógica de Banco de Dados restrita: Remoção bloqueada de entidades dependentes (ex: não é possível deletar um evento que possua concursos). SoftDeletes ativos para proteção de histórico.

---

## 📝 Licença
Este projeto foi desenvolvido como um software proprietário para gestão de eventos. Distribuído sob a licença [MIT](https://opensource.org/licenses/MIT).
