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

### Dia 17 — 📌 Alteração: Campo `bibliografia` → `descricao` + Exibição no Frontend e Desafio 2: Sistema de Livros Relacionados

**O que foi feito:**

-   Renomeada a coluna `bibliografia` para `descricao` na tabela `livros` para tornar o nome mais intuitivo e alinhado com a função real do campo.
-   Atualizados todos os controladores, serviços e views para refletir esta mudança.
-   Passado a exibir a descrição completa na página de detalhe (`show`) do livro.
-   Adicionado um excerto da descrição na listagem (`index`) para dar mais contexto ao utilizador.

**Motivo:**

-   O termo _bibliografia_ não representava corretamente o conteúdo armazenado (sinopse/resumo do livro).
-   Melhorar a clareza do código e a experiência do utilizador, permitindo que veja a descrição diretamente na plataforma.

**Modelo `Livro`:**

-   Implementado método `extractKeywordsFromDescricao` melhorado:

    -   Uso de `Str::ascii()` para remoção precisa de acentos.
    -   Limpeza de texto preservando espaços e evitando cortes de palavras.
    -   Filtro para ignorar palavras curtas, sem vogais ou presentes na lista de _stopwords_.
    -   Limite de 15 palavras-chave mais frequentes.

-   Atualização do método `relacionados`:
    -   Combinação de dois critérios:
        -   Livros com pelo menos 2 keywords em comum.
        -   Livros do mesmo autor (prioridade máxima).
    -   Ordenação final com “mesmo autor” no topo, seguido de afinidade temática.
    -   Remoção de duplicados com `unique('id')`.

---

**Controller:**

-   Ajuste no método `show` para carregar `$relacionados` e enviar para a view.

---

**View `livros.show`:**

-   Criação da secção **"Livros Relacionados"**.
-   Separação visual em dois grupos:
    -   ✍️ **Do mesmo autor**
    -   📌 **Semelhantes no tema**
-   Exibição de:
    -   Capa do livro.
    -   Nome, editora e autores.
    -   Até 5 keywords.
-   Badge “✍️ Do mesmo autor” para identificação rápida.

---

**Reprocessamento de Keywords:**

-   Execução de _backfill_ via Tinker para recalcular keywords de livros já existentes com a nova lógica.

---

**Testes realizados:**

-   Validação de keywords geradas (sem cortes e mais relevantes).
-   Verificação de sugestões coerentes por afinidade temática.
-   Confirmação de prioridade para livros do mesmo autor.
-   Teste da separação visual na interface.

---

### Dia 18 — 📌 Ajustes no fluxo de criação/edição + Integração com Google Books + Navegação persistente

**O que foi feito:**

-   Corrigido erro `"The PUT method is not supported for route livros"` ao criar novos livros.
-   Ajustado o `_form.blade.php` para funcionar corretamente tanto em criação como edição.
-   Protegidos acessos a `$livro` com `optional()` e validações de existência.
-   Corrigida a lógica de pré-seleção de géneros, autores e editora no formulário.
-   Adicionado suporte à criação manual de novos géneros e editoras.
-   Melhorada a integração com a Google Books API:
    -   Adicionada chave de API via `.env`.
    -   Otimizados parâmetros da chamada: `maxResults`, `fields`, `printType`, `langRestrict`.
    -   Reduzidas falhas e respostas incompletas.
-   Ajustada lógica de sugestão de géneros vindos da API para manter compatibilidade com a BD.
-   Reorganizada a página de detalhes (`livros.show`) com layout em duas colunas:
    -   Capa à esquerda.
    -   Detalhes, descrição e opiniões à direita.
    -   Espaçamento melhorado entre elementos.
-   Adicionada paginação dupla na listagem de livros (`index`):
    -   Exibição da navegação tanto no topo como no fundo da página.
-   Implementada persistência da página atual ao navegar entre listagem, detalhes e edição:
    -   Botões “Voltar” respeitam o número da página.
    -   Após editar, o utilizador regressa à mesma página da listagem.
    -   Parâmetro `page` transmitido via rota, campo oculto e redirecionamento.

**Motivo:**

-   Eliminar erros de navegação e inconsistências no formulário.
-   Melhorar a experiência do utilizador ao manter o contexto de navegação.
-   Tornar a integração com a API mais robusta e eficiente.
-   Aproveitar melhor o espaço visual na página de detalhes.
-   Garantir que os dados vindos da API são corretamente tratados e integrados.

**Controller:**

-   `LivroController@create`, `@edit` e `@update` ajustados para aceitar e preservar `page`.
-   Redirecionamento após atualização respeita a página anterior.

**View `livros.index`:**

-   Adicionada paginação no topo da listagem.
-   Botão “Editar” inclui parâmetro `page`.

**View `livros.edit`:**

-   Rota do formulário inclui `page`.
-   Botão “Voltar” redireciona para a página correta.

**View `_form.blade.php`:**

-   Campo oculto `page` incluído no formulário.
-   Ajustes na lógica de pré-preenchimento de campos vindos da API.

**View `livros.show`:**

-   Layout reorganizado em duas colunas.
-   Espaçamento ajustado entre disponibilidade e imagem.
-   Descrição e opiniões movidas para a coluna lateral.

**Testes realizados:**

-   Criação e edição de livros sem erros.
-   Validação da navegação entre páginas da listagem.
-   Verificação da persistência da página após editar ou visualizar detalhes.
-   Teste da integração com a Google Books API com chave ativa.
-   Visualização correta de géneros e autores vindos da API.
-   Teste da nova estrutura visual na página de detalhes.

---

### 📬 Desafio 3 — Alertas de Disponibilidade de Livros

Implementado sistema de alertas que permite aos cidadãos receberem notificações por email quando um livro requisitado por outro utilizador ficar disponível.

**Funcionalidades incluídas:**

-   Pedido de alerta por cidadão quando o livro está indisponível
-   Disparo automático de email após entrega do livro, se não houver requisições ativas
-   Template de email personalizado com capa, título e link direto para o livro
-   Assunto do email ajustado para “📚 Livro disponível para requisição”
-   Lógica que permite novo pedido de alerta caso o utilizador tenha sido notificado mas não requisitou o livro
-   Validação completa do fluxo com testes manuais

> Este sistema garante que os utilizadores são informados no momento certo e podem repetir o pedido de alerta sempre que necessário, sem duplicações indesejadas.

---

### Dia 19 — 📌 Ajustes finais e correções

-   Ocultação do botão “🔔 Avisar-me quando disponível” para o cidadão que já tem o livro requisitado
-   Correção da lógica de verificação de posse do livro (`cidadao_id` em vez de `user_id`)
-   Ajuste do botão “⬅️ Voltar” na página do livro com fallback para `livros.index` quando `url()->previous()` não é válido
-   Testes manuais ao fluxo de alerta e requisição para garantir consistência na experiência do utilizador

---

### Dia 20 — 📌 Validação Final e Apresentação

-   Realização de testes manuais aos três desafios:
    -   Verificação do fluxo completo de criação, moderação e visualização de reviews
    -   Testes à lógica de livros relacionados com base em palavras-chave e autor
    -   Simulação do alerta de disponibilidade com envio de email e comportamento do botão
-   Confirmação da navegação entre páginas de detalhe e retorno à listagem com o parâmetro `from`
-   Validação da experiência do utilizador em diferentes perfis (Cidadão e Admin)
-   Gravação do vídeo de apresentação com demonstração funcional dos três desafios

---

### Dia 22 — 🛒 Carrinho, Moradas de Entrega e Página de Pagamento (pré‑Stripe)

-   Implementação completa do **carrinho de compras**:
    -   Adição de livros com incremento automático de quantidade
    -   Atualização de quantidades com validação mínima
    -   Remoção de itens individualmente
    -   Cálculo automático do subtotal
-   Criação do **modelo `EnderecoEntrega`** e respetiva tabela:
    -   Campos: `user_id`, `nome`, `telefone`, `morada`, `codigo_postal`, `localidade`, `pais`
    -   Associação ao utilizador autenticado
-   Fluxo de **inserção e edição de morada**:
    -   Opção de guardar morada para reutilização futura ou usar apenas nesta compra
    -   Morada guardada na sessão para checkout atual
    -   Edição com formulário pré‑preenchido e validação de propriedade
-   **Integração da morada no carrinho**:
    -   Exibição da morada mais recente no carrinho, se existir
    -   Botão “Editar Morada” ou “Inserir Morada de Entrega” conforme o caso
-   **Melhorias de interface**:
    -   Botão “Voltar ao Carrinho” no formulário de morada
    -   Resumo da encomenda na coluna direita da página de morada (responsivo)
    -   Queries de carrinho movidas para o controlador para manter o Blade limpo
-   Criação da **página de pagamento** (`checkout.pagamento`):
    -   Mostra morada de entrega (sessão ou BD) e resumo da encomenda
    -   Botão placeholder “Pagar com Stripe” (integração a realizar na próxima fase)
    -   Botão “Voltar ao Carrinho”

---

### Dia 23 — 💳 Integração Stripe, Criação de Encomendas e Dashboard Administrativo

-   Implementação da **integração completa com Stripe Checkout**:

    -   Criação da sessão Stripe com os itens do carrinho
    -   Redirecionamento automático para Stripe e retorno para página de sucesso ou cancelamento
    -   Validação do carrinho antes de iniciar o pagamento

-   Gestão da **morada de entrega no fluxo Stripe**:

    -   Envio da morada como `hidden inputs` no formulário de pagamento
    -   Armazenamento da morada na sessão antes de criar a sessão Stripe
    -   Fallback para morada guardada na BD caso a sessão esteja vazia no retorno

-   Criação automática da **encomenda após pagamento**:

    -   Geração da encomenda com estado inicial `paga`
    -   Associação dos livros comprados com quantidade e preço unitário
    -   Limpeza do carrinho e da sessão após sucesso

-   Páginas de **sucesso** (`checkout.sucesso`) e **cancelamento** (`checkout.cancelado`):

    -   Feedback visual após pagamento
    -   Garantia de consistência no estado da encomenda

-   Atualização do **dashboard administrativo**:

    -   Contadores dinâmicos para:
        -   Total de livros
        -   Total de utilizadores
        -   Resenhas pendentes
        -   Requisições ativas
        -   Encomendas pendentes (em progresso)
    -   Visualização de encomendas com estado “paga” e respetivos detalhes

-   Melhorias na **interface de pagamento**:
    -   Verificação da existência de morada antes de mostrar botão Stripe
    -   Redirecionamento automático para preenchimento de morada se necessário
    -   Garantia de que o utilizador nunca entra no Stripe sem dados essenciais

---

### Dia 24 — 📦 Notificações, Carrinho, Encomendas e Gestão de Stock

-   Implementação do sistema de **notificação por abandono de carrinho**:

    -   Criação de job para envio de email após inatividade
    -   Template institucional com dados da última tentativa de compra
    -   Testes manuais com MailHog e validação de lógica de expiração

-   Validação de **quantidade no carrinho limitada ao stock disponível**:

    -   Campo `max` aplicado ao input de quantidade
    -   Aviso visual quando o limite é atingido
    -   Proteção contra tentativa de ultrapassar stock via formulário

-   Confirmação da lógica que **impede adicionar livros esgotados ao carrinho**:

    -   Botão ocultado na listagem de livros quando `stock_venda === 0`
    -   Validação no controller para impedir adição forçada

-   Criação de rotas e métodos para **gestão de encomendas no admin**:

    -   Métodos `pendentes()` e `pagas()` no `EncomendaController`
    -   Rotas `admin.encomendas.pendentes` e `admin.encomendas.pagas`
    -   View com título dinâmico e botão “Voltar ao Dashboard”

-   Criação da secção de **gestão de stock crítico**:

    -   Controller `LivroStockController@index`
    -   Rota `admin.livros.stock` definida
    -   View com listagem de livros com `stock_venda <= 5`, ordenados por escassez

-   Melhorias na **interface administrativa**:

    -   Botões de navegação adicionados em carrinho, encomendas e stock
    -   Layouts ajustados para consistência visual e semântica
    -   Proteção contra dados nulos (ex: encomendas sem utilizador ou livros)

-   Testes manuais realizados:

    -   Simulação de abandono de carrinho e envio de email
    -   Validação de limites de stock no carrinho
    -   Navegação entre views admin e confirmação de rotas
    -   Fluxo completo de encomenda paga vs pendente

---

### Dia 25 — 🛒 Histórico de Encomendas, Dashboard do Cidadão e Finalização da Entrega

-   Criação da página de **histórico de encomendas do cidadão**:

    -   Controller `EncomendaCidadaoController@index` fora da pasta `Admin`
    -   View `pages/encomendas/cidadao.blade.php` com layout consistente
    -   Listagem de encomendas do utilizador autenticado com estado, data e livros

-   Definição da **rota protegida** para cidadãos:

    -   Rota `encomendas.cidadao` acessível apenas a utilizadores autenticados
    -   Garantia de que o admin não vê esta secção no seu dashboard

-   Adição de botão de **retorno à página inicial** na view de encomendas:

    -   Botão “← Voltar à Página Inicial” com rota `home`
    -   Mantida a navegação reversível em todas as views

-   Reorganização da **interface do dashboard do cidadão**:

    -   Substituição de botões simples por cartões informativos
    -   Títulos, descrições e ações agrupadas por tema
    -   Separação clara entre funcionalidades do cidadão e do admin

-   Proteção contra **visualização duplicada por admins**:

    -   Condicional `@if(!auth()->user()->isAdmin())` aplicada à secção do cidadão
    -   Garantia de que o dashboard mostra apenas o que é relevante para cada perfil

-   Finalização da **gravação do vídeo de apresentação**:

    -   Demonstração do fluxo completo de compra, pagamento e gestão
    -   Validação visual e funcional de todas as melhorias da semana

---

### Dia 26, 27 e 28 — TAREFA 1 - Consulta de Logs (Admin)

-   **Migração** criada para a tabela `logs\_atividade` com campos: `id`, `user\_id`, `acao`, `descricao`, `created\_at`.

-   **Modelo `Log`** criado com relação `belongsTo(User::class)`.

-   **Controller `Admin\\LogController`**:
      - Método `index()` com listagem paginada dos logs mais recentes.
      - **Filtros adicionados**:
      - Por **utilizador** (`user\_id`) — permite ver apenas ações de um utilizador específico.
      - Por **módulo** (`modulo`) — permite filtrar ações por área funcional (ex.: `Livros`, `Requisições`, `Pagamentos`).
      - Paginação preserva os filtros ativos (`withQueryString()`).

-   **Rota protegida** `admin.logs.index` adicionada ao grupo de rotas admin.

-   **View `admin/logs/index.blade.php`**:
      - Formulário no topo com `<select>` para utilizador e módulo.
      - Tabela com colunas: Data/Hora, Utilizador, Módulo, ID Objeto, Alteração, IP, Browser.
      - Mensagem de “Nenhum registo encontrado” quando não há resultados.

-   **Integração no dashboard**:
      - Card “📜 Logs de Atividade” com link direto para a listagem.

-   **Segurança**:
      - Acesso restrito a utilizadores com perfil de administrador.

# Processo de registo de logs

-   Implementado via **trait `App\\Traits\\RegistaLog`**.
-   Método principal:
      (```php
      $this->registarLog(string $modulo, ?int $objetoId, string $alteracao);)
-   Preenche automaticamente:

    -   user_id (utilizador autenticado)
    -   ip (endereço IP)
    -   browser (user agent)

-   Modulo, objeto_id e alteracao são definidos no momento da chamada, garantindo consistência e clareza nos registos.
-   Todos os controllers auditados usam este trait, assegurando que o campo modulo está sempre preenchido e pronto para ser usado nos filtros.

# Dados recolhidos por log

-   **user_id:** utilizador autenticado associado ao evento (ou nulo se aplicável).
-   **modulo:** nome do módulo/área onde ocorreu a ação (ex.: `Livros`, `Requisições`, `Pagamentos`).
-   **objeto_id:** identificador do objeto afetado (se aplicável).
-   **alteracao:** descrição legível da ação realizada.
-   **ip:** endereço IP do utilizador no momento da ação.
-   **browser:** _user agent_ do navegador.
-   **created_at:** timestamp do registo.

# Pontos de integração (controllers/métodos)

Registo de logs adicionado nos seguintes controladores, através do **trait `RegistaLog`**:

-   **UserController** — criação, atualização e remoção de utilizadores.
-   **ReviewController** — aprovação e rejeição de reviews.
-   **RequisicaoController** — criação, aprovação, devolução de livros.
-   **LivroController** — criação, edição e remoção de livros.
-   **PagamentoController** — registo de pagamentos e alterações de estado.
-   **CarrinhoController** — adicionar/remover itens, checkout.
-   **Admin\LivroStockController** — atualização de stock.

# Ajustes no Dashboard

-   Cards expansíveis com `open: false` por defeito (inicialmente fechados).
-   Aplicado `items-start` na grid para que cada card expanda/recolha de forma independente.
-   Criado **Painel de Avisos Importantes** expansível (reviews pendentes, requisições ativas, stock crítico).
-   Adicionado botão **"Voltar ao Menu"** nas páginas de Gestão de Stock (`route('home')`).

## Cobertura de Logs — Autenticação

# Eventos registados

-   **Login bem-sucedido** (`Illuminate\Auth\Events\Login`)
-   **Tentativa de login falhada** (`Illuminate\Auth\Events\Failed`)

# Implementação

-   **Provider:** `App\Providers\EventServiceProvider`
    -   Regista os listeners para os eventos de autenticação.
-   **Listeners:**
    -   `App\Listeners\RegistarLogin`
        -   Usa o `trait App\Traits\RegistaLog` para criar um registo com:
            -   `modulo`: `Autenticação`
            -   `objeto_id`: ID do utilizador
            -   `alteracao`: `"Login bem-sucedido"`
            -   `ip` e `browser` capturados automaticamente
    -   `App\Listeners\RegistarFalhaLogin`
        -   Cria um registo direto em `logs` com:
            -   `user_id`: `null` (não autenticado)
            -   `modulo`: `Autenticação`
            -   `alteracao`: `"Tentativa de login falhada para o email: ..."`
            -   `ip` e `browser` capturados automaticamente

# Fluxo de funcionamento

1. O Laravel dispara o evento (`Login` ou `Failed`) durante o processo de autenticação.
2. O `EventServiceProvider` encaminha o evento para o listener correspondente.
3. O listener regista a ação na tabela `logs`.
4. O registo fica disponível na listagem de logs do admin e pode ser filtrado pelo módulo **"Autenticação"**.

## Cobertura de Logs — Utilizadores

# Eventos registados

-   **Criação de utilizador (Admin)** — `UserController@store`
    -   `modulo`: `Utilizadores`
    -   `objeto_id`: ID do utilizador criado
    -   `alteracao`: `"Criou o utilizador '{nome}' com o papel '{role}'"`
-   **Criação de utilizador (Registo público)** — `App\Actions\Fortify\CreateNewUser@create`
    -   `modulo`: `Utilizadores`
    -   `objeto_id`: ID do utilizador criado
    -   `alteracao`: `"Registo público do utilizador '{nome}' com o papel '{role}'"`
-   **Eliminação de utilizador** — `UserController@destroy`
    -   `modulo`: `Utilizadores`
    -   `objeto_id`: ID do utilizador eliminado
    -   `alteracao`: `"Apagou o utilizador '{nome}' com o papel '{role}'"`

# Implementação

-   **Trait:** `App\Traits\RegistaLog` usado em todos os pontos de criação/eliminação.
-   **Segurança:**
    -   Apenas admins podem criar ou apagar utilizadores no backoffice.
    -   Proteção para impedir que um admin apague a si próprio (`auth()->id() !== $user->id`).
-   **Views:**
    -   `pages/users/index.blade.php` — botão "Apagar" visível apenas para admins e não para o próprio.
    -   `pages/users/show.blade.php` — botão "Apagar Utilizador" com as mesmas restrições.
-   **Rotas:**
    -   `DELETE /users/{user}` → `users.destroy` (adicionado ao `Route::resource`).
-   **Registo público:**
    -   Implementado diretamente no `CreateNewUser` do Fortify, garantindo que também o registo feito pelo próprio cidadão é auditado.

---

### Dia 29 — TAREFA 2 Testes Automáticos com Pest

Este documento descreve os testes desenvolvidos para validar os principais fluxos de requisição de livros no sistema, conforme o enunciado da Tarefa 2. Os testes foram implementados com [Pest](https://pestphp.com/) e garantem que o comportamento da aplicação está alinhado com os requisitos funcionais.

## 🔍 Testes Implementados

# 1. Criação de Requisição de Livro

-   Criar um utilizador com papel de cidadão.
-   Criar um livro com stock disponível.
-   Submeter uma requisição para esse livro.
-   Verificar que a requisição foi criada com estado `'ativa'` e os dados estão corretos.

# 2. Validação de Requisição

-   Submeter uma requisição sem `livro_id`.
-   Submeter uma requisição com `livro_id` inexistente.
-   Verificar que o Laravel retorna erros de validação adequados.

# 3. Devolução de Livro

-   Criar uma requisição com estado `'ativa'`.
-   Submeter uma devolução via rota `PATCH`.
-   Verificar que o estado foi atualizado para `'entregue'` e que `data_fim_real` foi registada.

# 4. Listagem de Requisições por Utilizador

-   Criar múltiplas requisições para diferentes utilizadores.
-   Autenticar como um utilizador específico.
-   Verificar que apenas as suas requisições são listadas.

# 5. Stock na Encomenda de Livros

-   Criar um livro com `stock_venda = 0`.
-   Tentar criar uma requisição para esse livro.
-   Verificar que a operação é impedida com erro associado ao campo `livro_id`.

---

## Como Executar os Testes

# Recriar a base de dados de testes

php artisan migrate:fresh --env=testing

# Executar todos os testes

./vendor/bin/pest

# Executar apenas os testes de requisições

./vendor/bin/pest tests/Feature/RequisicoesTest.php

## Estrutura do Projeto

-   **Controller**

    -   Localização: `app/Http/Controllers/RequisicaoController.php`
    -   Responsável pela lógica de criação, devolução, listagem e validação de requisições.

-   **Testes**

    -   Localização: `tests/Feature/RequisicoesTest.php`
    -   Utiliza Pest para escrita clara e expressiva dos testes.
    -   Os testes são executados no ambiente `testing`, com base de dados isolada, guardada em \storage\database_test.sqlite.

-   **Ambiente de Testes**

    -   O ficheiro `.env.testing` define a configuração da base de dados de testes.
    -   Antes de correr os testes, a base de dados é recriada com:
        ```bash
        php artisan migrate:fresh --env=testing
        ```
    -   Esta abordagem garante que os testes correm num ambiente limpo e controlado;

-   **Factories**

    -   Localização:
        -   `database/factories/UserFactory.php`
        -   `database/factories/LivroFactory.php`
        -   `database/factories/RequisicaoFactory.php`
    -   Preparadas para gerar dados realistas e compatíveis com os testes.

-   **Migração**

    -   Tabela: `requisicoes`
    -   Campo relevante:
        ```php
        $table->enum('status', ['ativa', 'entregue'])->default('ativa');
        ```

-   **Modelo `User`**

    -   Métodos auxiliares:

        ```php
        public function isCidadao() {
            return $this->role === 'cidadao';
        }

        public function isAdmin() {
            return $this->role === 'admin';
        }
        ```

---

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

---

[📺 Ver vídeo relativo à quarta semana no Google Drive](https://drive.google.com/file/d/196tbGmfITc1uApwN98wZK1ixxaDuTI19/view?usp=sharing)

---

[📺 Ver vídeo relativo à quinta semana no Google Drive](https://drive.google.com/file/d/1I6tIq8x9TfsNoNoB1sVWX6n4lupCERh0/view?usp=sharing)

Desenvolvido por José G. durante estágio na InovCorp.
