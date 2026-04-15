## 1\. Visão Geral e Norte Criativo (Creative North Star)

**Norte Criativo: "Editorial Elétrico"** Este design system foi construído para unir a cultura vibrante de comunidades de fãs com a logística profissional de eventos. Estamos nos afastando de layouts "padrão SaaS" em direção a uma abordagem de "Palco Digital" — onde a interface (UI) parece uma sobreposição (overlay) de streaming de luxo ou uma revista editorial de edição limitada.

O sistema quebra a rigidez da grade tradicional através de assimetria intencional, sobreposição de containers e interações com estados de brilho ("glow-state"). Tratamos a interface como uma tela escura onde a informação é iluminada por acentos neon, mimetizando as luzes de um show de K-pop ou o brilho vibrante de um setup gamer.

## 2\. Cores e Lógica de Superfície

A paleta utiliza neons de alto contraste contra tons de obsidiana profundos e sofisticados para criar uma sensação de profundidade infinita.

### A Regra do "Sem Linhas" (No-Line Rule)

Para manter uma estética premium e contínua, **bordas sólidas de 1px são estritamente proibidas para separar seções**. Os limites devem ser definidos exclusivamente através de mudanças na cor do fundo. Use `surface-container-low` (`#131318`) para seções secundárias sobrepostas ao fundo principal `surface` (`#0e0e13`).

### Hierarquia de Superfície e Empilhamento

Trate a UI como camadas físicas de "Vidro de Obsidiana":

  * **Base:** `surface` (`#0e0e13`)
  * **Camada Secundária:** `surface-container-low` (`#131318`)
  * **Containers Flutuantes/Ativos:** `surface-container-high` (`#1f1f26`)
  * **Inputs Interativos:** `surface-container-highest` (`#25252c`)

### A Regra de "Vidro e Gradiente"

Para elementos flutuantes, como Scanners de QR Code ou modais de Placar (Leaderboard), utilize *glassmorphism*. Use a cor `surface-container` semitransparente com um `backdrop-filter: blur(20px)`. As CTAs (Chamadas para Ação) principais não devem ser planas; utilize um gradiente linear de `primary` (`#ed86ff`) para `primary-container` (`#e76eff`) em um ângulo de 135 graus para conferir "alma" visual.

## 3\. Tipografia: Energia vs. Utilidade

A estratégia tipográfica usa uma abordagem de "dupla personalidade" para equilibrar a energia da cultura pop com a densidade administrativa.

  * **Display e Títulos (Space Grotesk):** Usada para títulos de eventos, rankings e mensagens de alto impacto. Devem ser configuradas com espaçamento entre letras apertado (`-2%`) e pesos em negrito (**bold**) para parecerem "barulhentas" e energéticas.
  * **Títulos e Corpo (Manrope):** A base da plataforma. Proporciona uma sensação moderna e geométrica que mantém a legibilidade em cronogramas de eventos complexos.
  * **Dados e Rótulos (Inter):** Reservada especificamente para tabelas administrativas e rótulos de formulários. Use `label-sm` para metadados secundários para maximizar a densidade de informação sem poluir o visual.

## 4\. Elevação e Profundidade

A profundidade é alcançada através de **Camadas Tonais (Tonal Layering)** em vez de linhas estruturais tradicionais.

  * **Princípio de Camadas:** Em vez de sombras, coloque um card `surface-container-lowest` dentro de uma seção `surface-container-low` para criar um visual "rebaixado".
  * **Sombras Ambientes:** Para componentes flutuantes (Leaderboards), use sombras extra-difusas: `box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4)`. A sombra deve parecer oclusão de ambiente, não uma sombra projetada (**drop shadow**) dura.
  * **O Plano B da "Borda Fantasma":** Se um limite for exigido por acessibilidade, use `outline-variant` (`#48474d`) com 20% de opacidade. Nunca use bordas 100% opacas.

## 5\. Componentes

### Tabelas de Dados (Admin)

  * **Estrutura:** Sem linhas horizontais ou verticais. Use cores de linha alternadas (usando `surface-container-low`) ou simplesmente um espaçamento vertical generoso.
  * **Badges de Status:**
      * **APTO:** Texto `secondary` (`#00eefc`) sobre fundo `secondary` com 10% de opacidade.
      * **INAPTO:** Texto `error` (`#ff6e84`) sobre fundo `error` com 10% de opacidade.
  * **Interação:** As linhas devem "subir" sutilmente usando `surface-bright` (`#2c2b33`) ao passar o mouse (**hover**).

### Sliders de Pontuação Focados (Mobile-First)

  * **Trilha (Track):** Use `surface-container-highest`.
  * **Marcador (Thumb):** Um quadrado arredondado grande `xl` (`0.75rem`) usando o gradiente de `primary` para `primary-container`.
  * **Feedback Visual:** Conforme o slider se move, a trilha deve ser "preenchida" com um brilho neon `secondary` que vaza levemente para o fundo com um brilho externo suave.

### Widgets de Leaderboard (Placar)

  * **Top 3 Tiers:** Use `tertiary` (`#ff6b98`) para o 1º lugar, `primary` para o 2º e `secondary` para o 3º.
  * **Layout:** Use barras de altura assimétrica para os 3 primeiros, criando um efeito de "pódio" que quebra o ritmo de lista padrão.

### Scanners de QR Code

  * **Visor (Viewfinder):** Use uma moldura de "Borda Fantasma" com cantoneiras neon `secondary`.
  * **Animação:** Uma linha de varredura horizontal usando um gradiente de `secondary` (0% de opacidade para 100% para 0%) deve se mover verticalmente.

### Construtores de Formulários Complexos

  * **Campos de Entrada (Inputs):** Use `surface-container-highest` com um arredondamento de canto **DEFAULT** (`0.25rem`).
  * **Estado de Foco:** Em vez de uma borda grossa, use um brilho externo de `2px` em `primary_dim` (`#c400e9`) e mude o fundo para `surface-bright`.

## 6\. O Que Fazer e O Que Não Fazer (Do's and Don'ts)

### Fazer (Do):

  * **Use Assimetria Intencional:** Sobreponha um "Leaderboard Flutuante" levemente sobre uma seção de fundo para criar profundidade.
  * **Abrace o Alto Contraste:** Garanta que o texto `on-background` sempre esteja sobre uma camada de `surface` suficientemente escura.
  * **Use a Escala de Arredondamento:** Utilize `xl` para cards grandes e **DEFAULT** para botões funcionais para manter o visual "gamer" profissional.

### Não Fazer (Don't):

  * **Não Use Divisores:** Nunca use uma linha de `1px` para separar itens de lista. Use espaço em branco ou um degrau na escala de `surface-container`.
  * **Não Exagere no Brilho (Glow):** Acentos neon devem ser "luzes", não "planos de fundo". Mantenha grandes áreas de cor reservadas para neutros escuros para evitar fadiga ocular.
  * **Não Use Sombras Padrão (Material):** Evite as sombras padrão "Elevation 1-4". Atenha-se ao Tonal Layering e às Bordas Fantasmas.
