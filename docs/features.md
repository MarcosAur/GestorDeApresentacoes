# Funcionalidades Disponíveis

Este documento mapeia as capacidades do sistema divididas por perfil de usuário.

## 1. Perfil: Administrador (Admin)
O Admin possui controle total sobre a logística e dados do evento.
- **Gestão de Infra:** CRUD de Eventos, Concursos e Critérios de Avaliação (Baremas).
- **Gestão de Acesso:** Cadastro e vínculo de Jurados a concursos específicos.
- **Análise de Inscrições:** Revisão de documentos enviados por competidores, aprovação/reprovação com justificativa.
- **Check-in:** Scanner de QR Code para validar a presença física do competidor no dia do evento.
- **Controle de Palco:**
    - Ativação de apresentações (envio ao palco).
    - Monitoramento em tempo real do progresso das notas dos jurados.
    - Encerramento de apresentações e concursos.
- **Gestão de Rankings:** Visualização de resultados parciais e controle de liberação do ranking para o público.

## 2. Perfil: Jurado
Interface focada na experiência de avaliação rápida e precisa.
- **Feed em Tempo Real:** A tela de avaliação atualiza-se automaticamente assim que o Admin muda o competidor no palco.
- **Avaliação Técnica:** Atribuição de notas numéricas para critérios específicos via campos de texto decimais (0.0 a 10.0).
- **Independência:** Jurados não visualizam as notas uns dos outros durante o processo.

## 3. Perfil: Competidor
Interface para autogestão da inscrição.
- **Inscrição:** Envio de dados da obra/apresentação.
- **Gestão de Mídia:** Upload de arquivos MP3 e documentos de referência (bloqueados para deleção após envio para segurança).
- **Acompanhamento:** Status de aprovação (Apto/Inapto) e visualização do QR Code de check-in.

## 4. Perfil: Público
Visualização passiva de dados oficiais.
- **Stage Viewer:** Acompanhamento de quem está no palco e qual a próxima apresentação.
- **Histórico de Rankings:** Acesso aos resultados finais de concursos já encerrados e liberados pelo Admin.

---

## 5. Recursos Transversais
- **Real-time Sync:** WebSockets garantem que nenhuma tela precise de Refresh para refletir o estado atual do palco.
- **Ponderação Automática:** O sistema calcula notas finais baseadas em pesos e aplica critérios de desempate instantaneamente.
- **Segurança de Documentos:** Acesso a arquivos sensíveis via links assinados (S3) válidos por 5 minutos.
