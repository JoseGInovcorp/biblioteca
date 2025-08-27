@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-[70vh]">
    <div class="w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">🔐 Autenticação 2FA</h2>

        <p class="mb-4 text-sm text-gray-600">
            Introduza o código de autenticação fornecido pela sua aplicação ou um dos seus códigos de recuperação.
        </p>

        @if (session('status'))
            <div class="alert alert-success mb-4">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('