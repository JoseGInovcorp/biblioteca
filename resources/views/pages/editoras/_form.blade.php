@csrf
@if(isset($editora))
    @method('PUT')
@endif

<div>
    <label class="label">Nome</label>
    <input type="text" name="nome" value="{{ old('nome', $editora->nome ?? '') }}" class="input input-bordered w-full" required>
</div>

<div>
    <label class="label">Logótipo</label>
    <input type="file" name="logotipo" class="file-input file-input-bordered w-full">
    @if(isset($editora) && $editora->logotipo)
        <img src="{{ asset('storage/'.$editora->logotipo) }}" alt="{{ $editora->nome }}" class="mt-2 w-24 h-24 object-cover rounded">
    @endif
</div>

<div class="flex gap-2 pt-4">
    <button class="btn btn-primary">{{ isset($editora) ? 'Atualizar' : 'Criar' }}</button>
    <a href="{{ route('editoras.index') }}" class="btn btn-outline btn-secondary">⬅️ Voltar</a>
</div>