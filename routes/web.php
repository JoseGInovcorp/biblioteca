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
     * 📤 Exportação de livros para Excel (verificação no controller ou aqui)
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

    Route::resource('livros', LivroController::class);
    Route::resource('users', UserController::class)->only(['index', 'show', 'create', 'store']);

    //Rota temporaria do email para teste
    Route::get('/teste-mailhog', function () {
        $req = \App\Models\Requisicao::with('livro', 'cidadao')->latest()->first();
        $admins = \App\Models\User::where('role', 'admin')->pluck('email')->all();

        Mail::to($req->cidadao->email)
            ->bcc($admins)
            ->send(new \App\Mail\RequisicaoCriada($req));

        return 'Email enviado para o MailHog';
    });
});
