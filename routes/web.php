<?php

use Illuminate\Support\Facades\Route;
use App\Exports\LivrosExport;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\EditoraController;
use Maatwebsite\Excel\Facades\Excel;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    /**
     * Página inicial após login
     */
    Route::get('/', function () {
        return view('home'); // resources/views/home.blade.php
    })->name('home');

    /**
     * CRUDs principais
     */
    Route::resource('livros', LivroController::class);
    Route::resource('autores', AutorController::class)->parameters([
        'autores' => 'autor'
    ]);
    Route::resource('editoras', EditoraController::class);

    /**
     * Exportação de livros para Excel
     */
    Route::get('/exportar-livros', function () {
        return Excel::download(new LivrosExport, 'livros.xlsx');
    })->name('livros.exportar');

    /**
     * Dashboard (opcional, Jetstream)
     */
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
