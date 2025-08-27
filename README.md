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

### Dia 7

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

### Dia 7

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

---

## 📂 Funcionalidades

-   Autenticação com 2FA (Google Authenticator).
-   Gestão de Livros, Autores e Editoras (CRUD completo).
-   Upload e visualização de imagens.
-   Pesquisa, ordenação e filtros nas listagens.
-   Seleção múltipla de autores com TomSelect.
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

O vídeo de demonstração do projeto pode ser visto aqui:  
[📺 Ver vídeo no Google Drive](https://drive.google.com/file/d/1sqUylRn32b3t0sHrZI0jN22yGXUuAsDD/view?usp=sharing)

Desenvolvido por José G. durante estágio na InovCorp.
