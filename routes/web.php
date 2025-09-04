<?php

use App\Models\Requisicao;
use App\Mail\RequisicaoCriada;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Exports\LivrosExport;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\EditoraController;
use App\Http\Controllers\RequisicaoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GoogleBooksController;
use Maatwebsite\Excel\Facades\Excel;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Página inicial
    Route::get('/', function () {
        return view('home');
    })->name('home');

    /**
     * 📚 Livros
     * - index e show → todos autenticados
     * - create/store/edit/update/destroy → só admin (verificação no controller)
     */
    Route::resource('livros', LivroController::class);

    /**
     * ✍️ Autores
     */
    Route::resource('autores', AutorController::class)->parameters([
        'autores' => 'autor'
    ]);

    /**
     * 🏢 Editoras
     */
    Route::resource('editoras', EditoraController::class);

    /**
     * 📦 Requisições
     */
    Route::resource('requisicoes', RequisicaoController::class)
        ->parameters(['requisicoes' => 'requisicao']);

    /**
     * 📤 Exportação de livros para Excel
     */
    Route::get('/exportar-livros', function () {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }
        return Excel::download(new LivrosExport, 'livros.xlsx');
    })->name('livros.exportar');

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    /**
     * 👥 Utilizadores
     */
    Route::resource('users', UserController::class)->only(['index', 'show', 'create', 'store']);

    /**
     * 📚 Integração Google Books API
     */
    Route::middleware('can:isAdmin')->group(function () {
        Route::get('/google-books', [GoogleBooksController::class, 'index'])->name('google-books.index');
        Route::get('/google-books/search', [GoogleBooksController::class, 'search'])->name('google-books.search');
        Route::post('/google-books/import', [GoogleBooksController::class, 'import'])->name('google-books.import');
        Route::post('/google-books/prefill', [GoogleBooksController::class, 'prefill'])->name('google-books.prefill');
        Route::post('/google-books/prefill-edit/{livro}', [GoogleBooksController::class, 'prefillEdit'])->name('google-books.prefillEdit');
    });

    /**
     * ✉️ Rota temporária para teste de email no MailHog
     */
    Route::get('/teste-mailhog', function () {
        $req = Requisicao::with('livro', 'cidadao')->latest()->first();
        $admins = \App\Models\User::where('role', 'admin')->pluck('email')->all();

        Mail::to($req->cidadao->email)
            ->bcc($admins)
            ->send(new RequisicaoCriada($req));

        return 'Email enviado para o MailHog';
    });
});
