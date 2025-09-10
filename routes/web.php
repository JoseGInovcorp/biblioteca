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
        ->parameters(['requisicoes' => 'requisicao']);

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
    Route::resource('users', UserController::class)->only(['index', 'show', 'create', 'store']);

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
});
