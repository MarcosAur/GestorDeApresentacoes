# PRD: Sistema de Gestão de Concursos e Apresentações em Eventos

## 1\. Visão Geral e Descrição do Sistema

O sistema será uma plataforma projetada para substituir o uso de planilhas e e-mails dispersos na organização de eventos. Ele será capaz de receber o cadastro de competidores com apresentações e registrar jurados. O sistema deve suportar uma hierarquia onde um evento principal pode conter vários sub-eventos (concursos), cada um com seus respectivos competidores e um fluxo sequencial de apresentações no palco.

## 2\. Objetivo

O objetivo principal é permitir que eventos se cadastrem na plataforma com suas apresentações de diversos tipos e categorias, possibilitando que jurados avaliem os competidores de forma automatizada e que um vencedor seja apurado ao final por meio de um ranking.

## 3\. Perfis de Usuário

O sistema possuirá quatro perfis distintos de acesso:

  * **Administrador (Admin):**
      * Cria concursos, eventos e define categorias (ex: K-Pop, Cosplay, etc.).
      * Define os critérios de avaliação (quesitos) e os critérios de desempate.
      * Realiza o cadastro de jurados e competidores.
      * Aprova ou reprova o cadastro dos competidores, passando uma justificativa.
      * Controla a ordem das apresentações, chamando quem vai para o palco.
      * Acompanha de forma exclusiva o cálculo das notas e o ranking em tempo real.
  * **Jurado:**
      * Acessa o sistema via login ou link.
      * Visualiza no sistema apenas as informações da apresentação que está ativamente no palco no momento.
      * Atribui notas para cada critério predefinido, sem ter acesso às notas dadas pelos outros jurados.
      * Confirma o envio definitivo da avaliação.
  * **Competidor:**
      * Acessa com login ou link para enviar os dados e documentações da sua apresentação.
      * Acompanha o status da aprovação do seu cadastro (Apto/Inapto) e justificativas.
      * Visualiza as informações pertinentes ao evento, como data e sequência do cronograma de apresentações.
  * **Público:**
      * Acompanha em tempo real as informações básicas sobre quem está se apresentando no palco.
      * Visualiza os resultados e o ranking geral após o encerramento do concurso.

## 4\. Funcionalidades Principais

### 4.1. Módulo de Cadastros (CRUD)

  * Criação e edição de eventos, sub-eventos, jurados e competidores.
  * Configuração de modalidades e limites de vagas por evento.

### 4.2. Credenciamento e Check-in

  * No dia do evento, deve haver um sistema de credenciamento e check-in.
  * Para automatizar e facilitar, o sistema pode disparar um QR Code de confirmação para os competidores. Na entrada ou concentração do evento, um responsável pode escanear o QR Code, movendo automaticamente o status do competidor de "Inscrito" para "Presente/Check-in realizado".
  * Com base nesse check-in, o sistema gera a lista de competidores aptos e presentes para o Admin organizar a chamada ao palco.
  * Deve haver uma funcionalidade dentro da página do usuário competidor para gerar seu qrcode que será lido pelo usuário que está registrado o check-in.

### 4.3. Dinâmica de Palco e Avaliação em Tempo Real

  * **Controle de Palco:**
      * O administrador seleciona qual será a próxima apresentação da lista de presentes.
      * O admin deve ter uma lista com as apresentações que ainda não ocorreram em ordem, mas ter a possibilidade de selecionar qualquer uma da lista para ser a próxima.
      * Deve haver uma opção para marcar a apresentação corrente como encerrada. Esta deve validar se todos os jurados enviaram todas as avaliações para a apresentação.
  * **Sincronização de Telas:**
      * Imediatamente após a seleção do Admin, as telas dos jurados e do público são atualizadas com os dados de quem está no palco.
  * **Avaliação:**
      * Os jurados recebem o formulário (barema) em suas telas e inserem suas notas. O sistema coleta as respostas e compila os resultados imediatamente, fornecendo feedback ao Admin em tempo real.

## 5\. Estruturação dos Critérios de Avaliação

O sistema deve ser flexível para que o Admin cadastre modelos de avaliação (baremas) conforme o formato do concurso. Com base nas fontes, o sistema deve suportar:

  * **Tipos de Escala:**
      * Os jurados podem avaliar em escalas customizadas, como de 0 a 5 pontos ou 0 a 10 pontos.
  * **Critérios de Desempate:**
      * O sistema deve calcular desempates baseando-se em pesos. Por exemplo, a maior nota prioritária em "Originalidade/Inovação", seguida da nota em "Relevância".
      * O cálculo deve ser realizado em tempo real com base no peso do critério e dos critérios de desempate caso haja necessidade.
      * Deve ser apresentado um ranking em tempo real apenas para o admin enquanto o concurso não for finalizado.
      * Ao final do concurso todos devem poder ver o ranking final.
