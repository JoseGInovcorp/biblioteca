# 📚 Projeto Biblioteca

Aplicação de gestão de biblioteca desenvolvida em Laravel com Jetstream, Livewire, Tailwind CSS e DaisyUI, com autenticação 2FA, cifragem de dados sensíveis, exportação para Excel e CRUD completo para Livros, Autores e Editoras.

---

## 🚀 Tecnologias utilizadas

-   **Laravel 11**
-   **Laravel Jetstream** (Livewire)
-   **Tailwind CSS** + **DaisyUI**
-   **Laravel Excel** (maatwebsite/excel)
-   **SQLite** (desenvolvimento)
-   **TomSelect** (seleção múltipla de autores)
-   **Google Authenticator** (2FA)
-   **TablePlus** (gestão da base de dados)
-   **Mailhog** (simulador de envio de emails)

---

## 📅 Histórico de desenvolvimento

### Dia 1

-   Criado projeto de raiz:
    ```bash
    composer create-project laravel/laravel biblioteca
    cd biblioteca
    ```
-   Instalação do Livewire e Jetstream:
    ```bash
    composer require laravel/jetstream
    php artisan jetstream:install livewire
    npm install
    npm run dev
    php artisan migrate
    ```
-   Ativado login com **2FA** via perfil de utilizador (Google Authenticator + códigos de recuperação).
-   Instalação do Tailwind CSS e DaisyUI:
    ```bash
    npm install -D daisyui
    ```
-   Configuração do `tailwind.config.js` para incluir DaisyUI.
-   Estrutura base com DaisyUI:
    -   `resources/views/layouts/app.blade.php` (layout base)
    -   `resources/views/pages/home.blade.php` (página inicial com botões para Livros, Autores e Editoras)
    -   Rotas iniciais em `web.php`
-   Criadas migrações:
    -   `create_livros_table.php`
    -   `create_autores_table.php`
    -   `create_editoras_table.php`
    -   `create_autor_livro_table.php` (pivot)
-   Criados modelos `Livro`, `Autor` e `Editora`.
-   Acesso à BD via TablePlus.

---

### Dia 2

-   Criados seeders: `AutoresSeeder`, `EditorasSeeder`, `LivrosSeeder` e atualizado `DatabaseSeeder`.
-   Corrigido erro de pluralização no SQLite (`autors` → `autores`).
-   Ajustadas foreign keys na migration `autor_livro`.
-   Implementada **cifragem de dados**:
    -   Mutators no modelo `Livro` para cifrar/decifrar `isbn` e `bibliografia`.
    -   Testes via Tinker e BD.
-   Atualizado `LivrosSeeder` para aplicar mutators.
-   Repopulação da BD com dados realistas.
-   Instalado Laravel Excel:
    ```bash
    composer require maatwebsite/excel
    ```
-   Criado exportador específico para Livros.
-   Criada rota para exportar Excel (`livros.xlsx`) com dados decifrados.

---

### Dia 3

-   Criado repositório GitHub:  
    [https://github.com/JoseGInovcorp/biblioteca](https://github.com/JoseGInovcorp/biblioteca)

---

### Dia 5

-   Criados controllers para Livros, Autores e Editoras.
-   Criadas views Blade para listagem.
-   Ligação ao storage público para upload de imagens (capas, fotos, logótipos).
-   Formulários para upload e validação nos controllers.
-   CRUD completo para Livros, Autores e Editoras.
-   Visualização de capa do livro com opção de abrir em tamanho real.
-   Adicionada Página de login personalizada via Jetstream.
-   Utilizado TomSelect para seleção múltipla de autores no formulário de Livros.
-   Cifragem do nome do utilizador no modelo `User` (mutators `setNameAttribute` / `getNameAttribute`).
-   Comando Artisan para cifrar retroativamente nomes de utilizadores existentes.
-   Criação de novo repositório GitHub da Inovcorp com histórico limpo.
-   Tema DaisyUI **Silk** aplicado globalmente.

### Dia 6

-   Reconfiguração do ambiente de desenvolvimento num novo portátil.
-   Implementado sistema de permissões com dois perfis: **Admin** e **Cidadão**.
-   Criado menu “📦 Requisições” acessível a ambos os perfis.
-   Validação de disponibilidade de livros antes da requisição.
-   Limite de 3 requisições ativas por cidadão.
-   Requisição regista foto do cidadão, data de início e fim prevista (+5 dias).
-   Admin pode confirmar entrega e registar data real.
-   Filtro por status na listagem de requisições.
-   Indicadores no topo da página de requisições:
    -   Total de requisições ativas
    -   Requisições nos últimos 30 dias
    -   Livros entregues hoje
-   Histórico de requisições visível no detalhe de cada livro.
-   Criado `UserController` e views para listar utilizadores e mostrar o histórico de requisições por cidadão.
-   Navegação cruzada entre livros e cidadãos via histórico.

### Dia 8

-   Indicadores no topo da página de Requisições:
    -   Total de requisições ativas
    -   Requisições nos últimos 30 dias
    -   Livros entregues hoje
-   Gestão de Utilizadores:
    -   Criado fluxo protegido para criar novos utilizadores (Admin ou Cidadão) via painel
    -   Apenas Admins podem criar outros Admins
    -   Adicionada view e formulário `create.blade.php`
    -   Botão “➕ Novo Utilizador” visível apenas para Admins na listagem
-   Catálogo de Livros:
    -   Mostra estado de disponibilidade (“✅ Disponível” / “❌ Indisponível”)
    -   Botão 📦 Requisitar ativo apenas para Cidadãos e quando disponível
    -   Alterações aplicadas na listagem e no detalhe do livro
-   Fluxo de Requisição:
    -   Pré-seleção automática do livro ao clicar em “Requisitar” no catálogo/detalhe
    -   Removido botão “Ver” da lista de requisições por não ser requisito e não exibir informação relevante
-   Autenticação & Layout:
    -   Unificação de layout (`layouts.app`) para páginas de login, registo e recuperação de password
    -   Formulários centralizados na página
    -   Adicionado link “Criar conta de Cidadão” no login
    -   Personalização estendida a reset de password, verificação de email e autenticação 2FA
    -   Garantido que registos públicos criam sempre `role = cidadao`

### Dia 9

-   **Fluxo de criação de requisições**

    -   Admin pode criar requisições para qualquer cidadão, escolhendo no formulário.
    -   Aplicado o limite de 3 requisições ativas mesmo quando criadas por um Admin para outro cidadão, com mensagens de erro claras.
    -   Página “Criar Requisição” atualizada para Admins com campo de seleção de cidadão e mensagens de erro junto aos campos.
    -   Página Confirmar Devolução (antigo “Editar Requisição”) adaptada para registar devolução real e estado final.

-   **Emails**

    -   **Configuração de ambiente de desenvolvimento com MailHog** para pré‑visualização de emails:
        ```env
        MAIL_MAILER=smtp
        MAIL_HOST=127.0.0.1
        MAIL_PORT=1025
        MAIL_USERNAME=null
        MAIL_PASSWORD=null
        MAIL_ENCRYPTION=null
        MAIL_FROM_ADDRESS="no-reply@biblioteca.local"
        MAIL_FROM_NAME="Biblioteca Municipal"
        ```
        MailHog acessível via [http://localhost:8025].
    -   **Email de confirmação de requisição** (`RequisicaoCriada`):
        -   Enviado automaticamente para o cidadão e para todos os Admins.
        -   Inclui dados completos da requisição e capa do livro.
    -   **Email de lembrete** (`RequisicaoLembrete`):
        -   Enviado apenas ao cidadão, no dia anterior à data de entrega prevista.
        -   Inclui capa do livro, corrigido para carregar corretamente no MailHog e clientes de email (ajuste de `APP_URL` e `php artisan storage:link`).
        -   Lógica validada e testada via Tinker simulando datas de fim.
    -   Views de email ajustadas para consistência visual entre confirmação e lembrete.

-   **Agendamento**

    -   Criado `app/Console/Kernel.php` para agendar `requisicoes:enviar-lembretes` diariamente às 09:00.
    -   Testes de execução manual e via `php artisan schedule:work` para garantir funcionamento.

-   **Indicadores para Admin**

    -   Reimplementados no topo da listagem de requisições:
        -   Total de requisições ativas.
        -   Requisições nos últimos 30 dias.
        -   Livros entregues hoje.
    -   Visíveis apenas para utilizadores com perfil Admin.

-   **Ajustes e polimento**
    -   Detalhe do utilizador mostra histórico de requisições com numeração, capas e links para os livros.
    -   Lista de livros com coluna de capa clicável para todos os perfis.
    -   Corrigido alinhamento vertical dos botões na coluna “Ações” da lista de livros.
    -   Protegido o acesso: cidadãos não acedem à lista de utilizadores (verificado que existia esse erro); botão “Voltar” no detalhe do utilizador ajusta‑se ao perfil.

### Dia 10

-   **Privacidade e permissões no histórico de requisições**

    -   Ajustado o detalhe do livro para que:
        -   Cidadãos vejam apenas as suas próprias requisições.
        -   Admins continuem a ver todas as requisições associadas ao livro.
    -   Lógica aplicada também ao histórico no detalhe do cidadão.

-   **Pesquisa e ordenação**

    -   Corrigida pesquisa por ISBN:
        -   Removida cifragem do campo `isbn` no modelo `Livro`.
        -   Atualização manual dos valores via TablePlus.
        -   Pesquisa agrupada por nome/ISBN no controller.
    -   Adicionados filtros e ordenação nas páginas de Autores e Editoras.
    -   Reimplementado botão “➕ Criar Livro” na listagem, visível apenas para Admins.

-   **MailHog e testes de email**

    -   MailHog instalado e configurado localmente via `mailhog_windows_amd64.exe`.
    -   Testes de envio de email de confirmação e lembrete validados com sucesso.
    -   Email de lembrete disparado manualmente via:
        ```bash
        php artisan requisicoes:enviar-lembretes
        ```
    -   Interface de MailHog acessível via [http://localhost:8025](http://localhost:8025).

-   **Ajustes na interface**

    -   Botão “📥 Receber Livro” na listagem de requisições só aparece quando o status é “ativa”.
    -   Corrigido comportamento da listagem de livros e requisições para respeitar o perfil do utilizador.
    -   Indicadores no topo da página de requisições mantêm-se visíveis apenas para Admins.

-   **Preparação do vídeo de apresentação**
    -   Criado roteiro de demonstração com os seguintes tópicos:
        -   Registo e atribuição de perfis.
        -   Criação e gestão de livros.
        -   Requisição de livros com validações.
        -   Confirmação de devolução por Admin.
        -   Visualização de histórico por perfil.
        -   Envio e receção de emails via MailHog.
        -   Indicadores de gestão no menu de requisições.
    -   Vídeo gravado e pronto para entrega na plataforma da empresa.

### Dia 11 e 12 — Integração com Google Books API

-   Ligação à [Google Books API](https://developers.google.com/books/docs/v1/getting_started) para pesquisa e importação de livros.
-   Criado serviço `GoogleBooksService` com métodos:
    -   `byIsbn()` — pesquisa por ISBN com fallback para título.
    -   `searchByTitle()` — pesquisa por título com limite de resultados.
    -   `mapVolumeToLivro()` — mapeia os dados da API para o formato da BD.
-   Implementado cache de resultados para evitar chamadas repetidas.
-   Criada interface de pesquisa com filtro por ISBN ou título.
-   Resultados exibem capa, título, autores e editora.
-   Botão “Importar” disponível apenas para livros com ISBN válido.
-   Ao importar:
    -   Cria ou atualiza livro na BD.
    -   Cria ou associa autores e editora.
    -   Faz download da capa e guarda em `storage/app/public/capas`.
    -   Redireciona para a página do livro com mensagem de sucesso.
-   Validação de dados e normalização de nomes para evitar duplicações.
-   Proteção contra autores inválidos (ex.: nomes numéricos ou vazios).
-   Funcionalidade acessível apenas a utilizadores com perfil Admin.

### Dia 13 — Melhoria do fluxo de criação de livros com dados da Google Books API

-   Formulário de criação de livros agora aceita preenchimento automático com dados vindos da Google Books API.
-   Adicionados campos ocultos para:
    -   `imagem_capa` — URL da capa sugerida pela API.
    -   `editora_nome` — nome da editora sugerida.
    -   `autores_nomes[]` — nomes dos autores sugeridos.
-   Pré-visualização da capa exibida no formulário mesmo sem upload manual.
-   No `LivroController@store`:
    -   Download automático da capa via URL e armazenamento em `storage/app/public/capas`.
    -   Criação dinâmica de autores e editora se não existirem.
    -   Validação e fallback para editoras novas introduzidas manualmente (`nova_editora`).
-   No `LivroController@update`:
    -   Alinhamento da lógica de substituição da capa (upload ou URL).
    -   Criação de nova editora se não selecionada.
    -   Remoção da capa anterior ao atualizar.
-   Views atualizadas para exibir corretamente a capa com `asset('storage/...')`.
-   Proteção contra autores inválidos (ex.: nomes numéricos ou vazios).
-   Testes realizados com livros com e sem editora vindos da API.
-   Funcionalidade acessível apenas a utilizadores com perfil Admin.

### Dia 14 — Alinhamento do fluxo de importação/edição com dados da Google Books API e correção de capas

-   **GoogleBooksController@import**:

    -   Passou a aceitar `nova_editora` no momento da importação.
    -   Criação/associação de nova editora se preenchido, caso contrário usa `editora_nome` da API ou “Editora Desconhecida”.
    -   Lógica de gravação da capa alinhada com `store()`/`update()`:
        -   Download da imagem via URL.
        -   Armazenamento em `storage/app/public/capas` com caminho relativo (`capas/ficheiro.jpg`).
        -   Compatível com `asset('storage/...')`, eliminando erros 403.
    -   Criação dinâmica de autores se não existirem.
    -   Proteção contra nomes inválidos (vazios ou numéricos).

-   **LivroController@store** e **LivroController@update**:

    -   Lógica unificada para gestão de editoras:
        -   Aceita `editora_id` ou `nova_editora` (`required_without`).
        -   Cria/associa nova editora mesmo que já exista uma associada.
        -   Mantida compatibilidade com `editora_nome` vindo da API.
    -   Substituição da capa (upload manual ou URL) com remoção da anterior.
    -   Validação consistente com o fluxo de importação.

-   **Views**:

    -   `_form.blade.php`:
        -   Campo “Ou criar nova editora” limpa automaticamente o `select` e vice‑versa.
    -   `google-books.index`:
        -   Adicionado campo “Nova editora (opcional)” nos formulários de importação e atualização.
    -   `index.blade.php` e `show.blade.php`:
        -   Exibição de capas usando `asset('storage/...')` para compatibilidade total.

-   **Correções**:

    -   Resolvido problema em que capas importadas da Google Books não eram exibidas (403 Forbidden).
    -   Garantido que todos os fluxos (criar, editar, importar) usam o mesmo método de gravação de capas.

-   **Testes realizados**:
    -   Importação de livros novos com e sem editora.
    -   Atualização de livros existentes com substituição de editora e capa.
    -   Verificação de acessibilidade das capas via browser.

### Dia 15 — Finalização do fluxo de criação manual com dados da Google Books API

**GoogleBooksController@prefill**:

-   Pré-preenchimento do formulário de criação com dados da API.
-   Envio de autores sugeridos (`autores_nomes[]`) como input oculto.

**LivroController@store**:

-   Criação dinâmica de autores sugeridos pela API mesmo quando o utilizador valida manualmente.
-   Autores que ainda não existem são apresentados como opções no `<select>`.
-   Identificadores temporários (`novo_nome`) tratados e convertidos em autores reais no momento da gravação.
-   Validação ajustada para aceitar autores dinâmicos sem bloquear o processo.
-   Mantida compatibilidade com `autores_nomes[]` como fallback.

**Views**:

-   `_form.blade.php`:
    -   Autores sugeridos pela API aparecem como opções no `<select>`, mesmo que ainda não existam na base de dados.
-   `google-books.index`:
    -   Adicionado botão “⬅️ Voltar para Lista de Livros” para facilitar navegação.

**Correções**:

-   Resolvido problema em que autores sugeridos pela API não eram criados no fluxo de criação manual.
-   Garantido que tanto a importação direta como a criação manual mantêm consistência na associação de autores.

**Testes realizados**:

-   Criação de livro via formulário com dados pré-preenchidos da API.
-   Validação manual com seleção de autores sugeridos.
-   Criação automática de autores não existentes.
-   Verificação da associação correta dos autores ao livro.

### Dia 16 — Implementação do Módulo de Reviews e Reestruturação do Menu Admin

**Base de Dados:**

-   Criação da tabela `reviews` com os campos:

    -   `id`
    -   `user_id` (FK para cidadãos)
    -   `livro_id` (FK para livros)
    -   `comentario`
    -   `estado` (`suspenso`, `ativo`, `recusado`)
    -   `justificacao` (opcional)
    -   `timestamps`

-   Relações definidas:
    -   `Review` pertence a `User` e a `Livro`
    -   `Livro` tem muitas `Review`
    -   `User` tem muitas `Review`

**Submissão de Reviews:**

-   Cidadãos podem submeter uma review após requisitar e devolver um livro.
-   Estado inicial da review: `suspenso`.
-   Proteção de rota: apenas cidadãos autenticados podem submeter.

**Moderação de Reviews:**

-   Página de moderação acessível via `/admin/reviews` (rota `reviews.index`).
-   Listagem de reviews pendentes com:

    -   Nome do cidadão
    -   Livro associado
    -   Comentário
    -   Formulário para aprovar ou recusar
    -   Campo de justificação (caso de recusa)
    -   Link para visualizar o livro

-   Método `update` no `ReviewController` para alterar estado da review.

---

**Notificações por Email:**

-   Para o admin:

    -   Email enviado quando uma nova review é submetida.
    -   Inclui link seguro com redirecionamento pós-login para a página de moderação.

-   Para o cidadão:
    -   Email enviado após moderação.
    -   Informa se a review foi aprovada ou recusada.
    -   Inclui justificação (se aplicável).
    -   Se aprovada, inclui link para o livro.

---

**Exibição Pública:**

-   Apenas reviews com estado `ativo` são exibidas no detalhe do livro.
-   View `livros.show` atualizada para listar:
    -   Nome do cidadão
    -   Comentário
    -   Data da review (opcional)

---

**Rota Técnica de Redirecionamento:**

-   Rota `/moderacao/reviews` criada para:
    -   Guardar destino na sessão (`url.intended`)
    -   Redirecionar para login se necessário
    -   Levar o admin diretamente à página de moderação após login

---

**Acesso Rápido no Menu:**

-   Botão “📝 Moderar Reviews” adicionado ao `home.blade.php`, visível apenas para admins.
-   Permite acesso direto à página de moderação sem depender do email.

---

**Histórico de Reviews Moderadas:**

-   Página de moderação expandida para incluir:

    -   Reviews pendentes (`suspenso`)
    -   Reviews aprovadas (`ativo`)
    -   Reviews recusadas (`recusado`)

-   Cada secção com contador e visualização clara.

---

**Menu Principal (`home.blade.php`):**

-   Reestruturação visual para admins:
    -   Separação por categorias:
        -   📦 Catálogo: Livros, Autores, Editoras
        -   👥 Gestão: Utilizadores, Reviews
        -   ➕ Ações Rápidas: Criar novo livro, autor, editora
    -   Uso de cards com títulos, ícones e descrições.
    -   Layout responsivo com `grid`, `shadow` e botões organizados.

**Dashboard de Contadores:**

-   Painel exclusivo para admins no topo da página.
-   Mostra:
    -   📚 Total de livros
    -   👥 Total de utilizadores
    -   📝 Reviews pendentes
    -   📦 Requisições ativas
-   Dados carregados via `HomeController@index` e enviados para a view.

**Testes realizados:**

-   Submissão de review por cidadão.
-   Moderação por admin com aprovação e recusa.
-   Verificação da exibição pública das reviews aprovadas.
-   Receção de emails por admin e cidadão.
-   Navegação direta via menu e via link do email.
-   Validação da nova estrutura visual do menu e dashboard.

---

### 📌 Alteração: Campo `bibliografia` → `descricao` + Exibição no Frontend

**O que foi feito:**

-   Renomeada a coluna `bibliografia` para `descricao` na tabela `livros` para tornar o nome mais intuitivo e alinhado com a função real do campo.
-   Atualizados todos os controladores, serviços e views para refletir esta mudança.
-   Passado a exibir a descrição completa na página de detalhe (`show`) do livro.
-   Adicionado um excerto da descrição na listagem (`index`) para dar mais contexto ao utilizador.

**Motivo:**

-   O termo _bibliografia_ não representava corretamente o conteúdo armazenado (sinopse/resumo do livro).
-   Melhorar a clareza do código e a experiência do utilizador, permitindo que veja a descrição diretamente na plataforma.

## 📂 Funcionalidades

-   Autenticação com 2FA (Google Authenticator).
-   Gestão de Livros, Autores e Editoras (CRUD completo).
-   Upload e visualização de imagens.
-   Pesquisa, ordenação e filtros nas listagens.
-   Seleção múltipla de autores com TomSelect.
-   Criação dinâmica de autores sugeridos pela Google Books API.
-   Validação inteligente de autores no formulário de criação (identificadores temporários).
-   Cifragem de dados sensíveis (`isbn`, `bibliografia`, `name` do utilizador).
-   Exportação de Livros para Excel.
-   Tema personalizável com DaisyUI.
-   Sistema de permissões com perfis Admin e Cidadão.
-   Requisição de livros com validações e limite por utilizador.
-   Histórico de requisições por livro e por cidadão.
-   Filtro por status nas requisições.
-   Indicadores estatísticos na página de requisições.
-   Navegação cruzada entre livros e cidadãos.

## 🎥 Vídeo de Apresentação

O vídeo de demonstração do projeto pode ser visto aqui para cada semana:  
[📺 Ver vídeo relativo à primeira semana no Google Drive](https://drive.google.com/file/d/1sqUylRn32b3t0sHrZI0jN22yGXUuAsDD/view?usp=sharing)

---

[📺 Ver vídeo relativo à segunda semana no Google Drive](https://drive.google.com/file/d/1IzSi-GE5zXihuQ4H9PYxdq78vfYXtAPf/view?usp=sharing)

---

[📺 Ver vídeo relativo à terceira semana no Google Drive](https://drive.google.com/file/d/1fgwtMZMhHvDYDBg1g7JZI49P_-Jifez8/view?usp=sharing)

Desenvolvido por José G. durante estágio na InovCorp.
