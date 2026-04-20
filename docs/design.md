# Design System: Plataforma de Gestão de Concursos

## 1. Stack Tecnológica

  * **Backend:** Laravel 12 (API RESTful)
  * **Frontend:** Vue 3 (SPA) + Tailwind CSS
  * **Real-time:** Laravel Reverb (WebSockets)
  * **Autenticação:** Laravel Sanctum (SPA)
  * **Banco de Dados:** MySQL 8.0 (Ver `database.md`)
  * **Armazenamento:** Amazon S3 (via Flysystem) para documentos e mídias.
  * **Arquitetura:** MVC + Service Layer + Princípios SOLID (Ver `architecture.md`)

-----

## 2. Estratégia Real-Time

Para evitar o uso de *polling*, a interface utiliza **Laravel Echo** ouvindo eventos disparados pelo backend.

  * **Motor:** **Laravel Reverb**.
  * **Eventos:** 
    - `ApresentacaoAlterada`: Disparado ao mudar o `current_presentation_id`. Reset automático do formulário do jurado e troca de contexto no palco público.
    - `NotaAtribuida`: Notifica o Admin sobre o progresso das avaliações em tempo real.


-----

## 4. Camada de Negócio (Services)

  * A lógica é isolada em `app/Services`.
  * **Padrão:** Cada operação deve ter um service próprio com um método estático de entrada `run(...)` (ou métodos semânticos como `evaluate()`) recebendo dados validados.

-----

## 3. Diretrizes de Frontend

Para detalhes de cores, tipografia e o conceito de "Editorial Elétrico", consulte o arquivo `frontend.md`.

## 4. Camadas de Sistema
A plataforma segue uma estrutura rigorosa de separação de responsabilidades:
- **`app/Services`**: Regras de negócio.
- **`app/Http/Controllers/Api`**: Endpoints REST.
- **`resources/js/views`**: Telas SPA.

Consulte `architecture.md` para padrões técnicos e `features.md` para o mapeamento de telas por perfil.
