@csrf
@if(isset($autor))
    @method('PUT')
@endif

<div>
    <label class="label">Nome</label>
    <input type="text" name="nome" value="{{ old('nome', $autor->nome ?? '') }}" class="input input-bordered w-full" required>
</div>

<div>
    <label class="label">Foto</label>
    <input type="file" name="foto" class="file-input file-input-bordered w-full">
    @if(isset($autor) && $autor->foto)
        <img src="{{ asset('storage/'.$autor->foto) }}" alt="{{ $autor->nome }}" class="mt-2 w-24 h-24 object-cover rounded-full">
    @endif
</div>

<div class="flex gap-2 pt-4">
    <button class="btn btn-primary">{{ isset($autor) ? 'Atualizar' : 'Criar' }}</button>
    <a href="{{ route('autores.index') }}" class="btn btn-outline btn-secondary">⬅️ Voltar</a>
</div>