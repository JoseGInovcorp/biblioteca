<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequisicaoCriada;
use App\Models\Requisicao;
use App\Models\User;
use App\Exports\LivrosExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\EditoraController;
use App\Http\Controllers\RequisicaoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GoogleBooksController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AlertaLivroController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\EnderecoEntregaController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\Admin\EncomendaController;
use App\Http\Controllers\Admin\LivroStockController;
use App\Http\Controllers\EncomendaCidadaoController;

/**
 * 🔹 Rota “ponte” para moderação de reviews
 */
Route::get('/moderacao/reviews', function () {
    if (auth()->check() && auth()->user()->isAdmin()) {
        return redirect()->route('reviews.index');
    }
    session()->put('url.intended', route('reviews.index'));
    return redirect()->route('login');
})->name('moderacao.reviews.link');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Página inicial com dashboard de contadores
    Route::get('/', [HomeController::class, 'index'])->name('home');

    /** 📚 Livros */
    Route::resource('livros', LivroController::class);

    /** 🔔 Alerta de disponibilidade de livro */
    Route::post('/livros/{livro}/alerta', [AlertaLivroController::class, 'store'])->name('alertas.store');

    /** ✍️ Autores */
    Route::resource('autores', AutorController::class)->parameters([
        'autores' => 'autor'
    ]);

    /** 🏢 Editoras */
    Route::resource('editoras', EditoraController::class);

    /** 📦 Requisições */
    Route::resource('requisicoes', RequisicaoController::class)
        ->only(['index', 'create', 'store', 'show', 'update', 'destroy'])
        ->parameters(['requisicoes' => 'requisicao']);

    // ➕ Rota extra para devolução de livros
    Route::patch('/requisicoes/{requisicao}/devolver', [RequisicaoController::class, 'devolver'])
        ->name('requisicoes.devolver');

    /** 📤 Exportação de livros para Excel */
    Route::get('/exportar-livros', function () {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }
        return Excel::download(new LivrosExport, 'livros.xlsx');
    })->name('livros.exportar');

    /** 🧭 Dashboard genérico */
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    /** 👥 Utilizadores */
    Route::resource('users', UserController::class)
        ->only(['index', 'show', 'create', 'store', 'destroy']);

    /** 📚 Integração Google Books API */
    Route::middleware('can:isAdmin')->group(function () {
        Route::get('/google-books', [GoogleBooksController::class, 'index'])->name('google-books.index');
        Route::get('/google-books/search', [GoogleBooksController::class, 'search'])->name('google-books.search');
        Route::post('/google-books/import', [GoogleBooksController::class, 'import'])->name('google-books.import');
        Route::post('/google-books/prefill', [GoogleBooksController::class, 'prefill'])->name('google-books.prefill');
        Route::post('/google-books/prefill-edit/{livro}', [GoogleBooksController::class, 'prefillEdit'])->name('google-books.prefillEdit');
    });

    /** ✉️ Teste de email via MailHog */
    Route::get('/teste-mailhog', function () {
        $req = Requisicao::with('livro', 'cidadao')->latest()->first();
        $admins = User::where('role', 'admin')->pluck('email')->all();

        Mail::to($req->cidadao->email)
            ->bcc($admins)
            ->send(new RequisicaoCriada($req));

        return 'Email enviado para o MailHog';
    });

    /** 💬 Reviews */
    Route::middleware(['auth'])->group(function () {
        Route::post('/requisicoes/{requisicao}/review', [ReviewController::class, 'store'])->name('reviews.store');
        Route::get('/admin/reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::patch('/admin/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    });

    // 🛒 Carrinho
    Route::post('/carrinho/add/{livro}', [CarrinhoController::class, 'add'])->name('carrinho.add');
    Route::get('/carrinho', [CarrinhoController::class, 'index'])->name('carrinho.index');
    Route::delete('/carrinho/{livro}', [CarrinhoController::class, 'remove'])->name('carrinho.remove');
    Route::patch('/carrinho/{livro}', [CarrinhoController::class, 'update'])->name('carrinho.update');

    // 📦 Endereços de entrega
    Route::get('/checkout/endereco', [EnderecoEntregaController::class, 'create'])->name('checkout.endereco');
    Route::post('/checkout/endereco', [EnderecoEntregaController::class, 'store'])->name('checkout.endereco.store');
    Route::get('/checkout/endereco/{endereco}/editar', [EnderecoEntregaController::class, 'edit'])->name('checkout.endereco.edit');
    Route::put('/checkout/endereco/{endereco}', [EnderecoEntregaController::class, 'update'])->name('checkout.endereco.update');

    // 💳 Página de pagamento (simulada)
    Route::get('/checkout/pagamento', function () {
        return view('pages.checkout.pagamento');
    })->name('checkout.pagamento');
    Route::post('/checkout/stripe', [PagamentoController::class, 'checkout'])->name('checkout.stripe');
    Route::get('/checkout/sucesso', [PagamentoController::class, 'sucesso'])->name('checkout.sucesso');
    Route::get('/checkout/cancelado', [PagamentoController::class, 'cancelado'])->name('checkout.cancelado');

    // 📋 Encomendas (Admin)
    Route::get('/admin/encomendas', [EncomendaController::class, 'index'])->name('admin.encomendas.index');
    Route::get('/admin/encomendas/pendentes', [EncomendaController::class, 'pendentes'])->name('admin.encomendas.pendentes');
    Route::get('/admin/encomendas/pagas', [EncomendaController::class, 'pagas'])->name('admin.encomendas.pagas');

    // 📋 Encomendas (Cidadão)
    Route::get('/minhas-encomendas', [EncomendaCidadaoController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('encomendas.cidadao');

    // 📉 Gestão de stock de livros (Admin)
    Route::get('/admin/livros/stock', [LivroStockController::class, 'index'])->name('admin.livros.stock');
    Route::put('/admin/livros/stock/{livro}', [LivroStockController::class, 'update'])
        ->name('admin.livros.stock.update')
        ->middleware('can:isAdmin');
    Route::get('/admin/livros/stock/todos', [LivroStockController::class, 'todos'])
        ->name('admin.livros.stock.todos')
        ->middleware('can:isAdmin');

    // 📜 Logs (Admin)
    Route::get('/admin/logs', [\App\Http\Controllers\Admin\LogController::class, 'index'])
        ->middleware(['auth', 'verified', 'can:isAdmin'])
        ->name('admin.logs.index');
});
