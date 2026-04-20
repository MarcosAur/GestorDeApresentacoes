# PRD: Sistema de Gestão de Concursos e Apresentações em Eventos

## 1. Visão Geral e Descrição do Sistema

O sistema é uma plataforma projetada para centralizar a organização de concursos em eventos. Ele permite o cadastro de competidores, apresentações e jurados, suportando uma hierarquia onde um evento principal contém vários concursos, cada um com seu fluxo de palco e critérios de avaliação exclusivos.

## 2. Objetivo

Eliminar a dependência de planilhas e processos manuais, automatizando a avaliação técnica, o controle de palco em tempo real e a apuração instantânea de rankings.

## 3. Perfis de Usuário

O sistema possui quatro perfis distintos de acesso:

  * **Administrador (Admin):**
      * Gerencia o ciclo de vida completo do evento (CRUD de concursos, critérios e jurados).
      * Analisa e valida inscrições de competidores.
      * Realiza o check-in físico via scanner de QR Code.
      * Controla o fluxo de palco (próxima apresentação, encerramento).
      * Monitora rankings parciais e decide o momento da liberação pública.
  * **Jurado:**
      * Avalia exclusivamente a apresentação ativa no palco.
      * Atribui notas decimais precisas baseadas nos quesitos técnicos (Barema).
  * **Competidor:**
      * Realiza o upload de documentos e mídias (MP3/Referências) de até 5MB.
      * Acompanha o status da sua inscrição e acessa seu QR Code de check-in.
  * **Público:**
      * Visualiza quem está no palco em tempo real.
      * Acessa os resultados oficiais após a finalização e liberação pelo Admin.

## 4. Funcionalidades Implementadas

### 4.1. Módulo de Real-time
Utilização de WebSockets (Laravel Reverb) para sincronizar as telas de Admin, Jurados e Público instantaneamente após comandos administrativos no palco.

### 4.2. Credenciamento e Check-in
Sistema de QR Code único por inscrição, permitindo que o Admin valide a presença com a câmera do dispositivo, movendo o competidor para a lista de "Aptos para Palco".

### 4.3. Algoritmo de Ranking Ponderado
Cálculo automático de pontuações considerando os pesos de cada critério e aplicando regras de desempate baseadas na prioridade definida pelo Admin.

### 4.4. Gestão de Documentos Segura
Armazenamento na nuvem (S3) com links assinados temporários, garantindo que arquivos de áudio e referências sejam acessados apenas por pessoal autorizado durante o evento.

