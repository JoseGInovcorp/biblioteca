@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-[70vh]">
    <div class="w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">🔄 Redefinir Password</h2>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label for="email" class="block font-semibold">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required class="input input-bordered w-full">
                @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block font-semibold">Nova Password</label>
                <input id="password" type="password" name="password" required class="input input-bordered w-full">
                @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block font-semibold">Confirmar Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required class="input input-bordered w-full">
            </div>

            <button type="submit" class="btn btn-primary w-full">Guardar nova password</button>
        </form>
    </div>
</div>
@endsection
