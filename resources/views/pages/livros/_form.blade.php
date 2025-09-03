@csrf
@if(isset($livro))
    @method('PUT')
@endif

@if(session()->hasOldInput())
    <div class="mb-4 p-3 bg-yellow-100 border border-yellow-300 text-yellow-800 rounded">
        Os campos foram preenchidos automaticamente com dados da Google Books. Revise e complete antes de gravar.
    </div>
@endif

@if(old('editora_nome'))
    <input type="hidden" name="editora_nome" value="{{ old('editora_nome') }}">
@endif

@foreach(old('autores_nomes', []) as $autorNome)
    <input type="hidden" name="autores_nomes[]" value="{{ $autorNome }}">
@endforeach

<div>
    <label class="label">Nome</label>
    <input type="text" name="nome" value="{{ old('nome', $livro->nome ?? '') }}" class="input input-bordered w-full" required>
</div>

<div>
    <label class="label">ISBN</label>
    <input type="text" name="isbn" value="{{ old('isbn', $livro->isbn ?? '') }}" class="input input-bordered w-full" required>
</div>

@php
    $editoraPrefillId = old('editora_id') ?? $editoras->firstWhere('nome', old('editora_nome'))?->id ?? 'nova_' . \Illuminate\Support\Str::slug(old('editora_nome'));
@endphp

<div>
    <label class="label">Editora</label>
    <select name="editora_id" class="select select-bordered w-full">
        <option value="">Selecione</option>
        @foreach($editoras as $editora)
            <option value="{{ $editora->id }}"
                @selected(old('editora_id', $livro->editora_id ?? $editoraPrefillId) == $editora->id)>
                {{ $editora->nome }}
            </option>
        @endforeach
    </select>
</div>

<div class="mt-2">
    <label class="label">Ou criar nova editora</label>
    <input type="text" name="nova_editora" value="{{ old('nova_editora') }}" class="input input-bordered w-full" placeholder="Digite o nome da nova editora">
</div>

@php
    $autoresPrefill = collect(old('autores_nomes', []));
@endphp

<div>
    <label class="label">Autores</label>
    <select name="autores[]" multiple id="autores-select" class="tom-select w-full">
        @foreach($autores->unique('id') as $autor)
            @php
                $isSelected = collect(old('autores', []))->contains($autor->id)
                    || collect(old('autores_nomes', []))->contains($autor->nome);
            @endphp
            <option value="{{ $autor->id }}" @if($isSelected) selected @endif>
                {{ $autor->nome }}
            </option>
        @endforeach
    </select>
</div>

<div>
    <label class="label">Bibliografia</label>
    <textarea name="bibliografia" class="textarea textarea-bordered w-full">{{ old('bibliografia', $livro->bibliografia ?? '') }}</textarea>
</div>

<div>
    <label class="label">Preço (€)</label>
    <input type="number" step="0.01" name="preco" value="{{ old('preco', $livro->preco ?? '') }}" class="input input-bordered w-full" required>
</div>

<div>
    <label class="label">Imagem da Capa</label>
    <input type="file" name="imagem_capa" class="file-input file-input-bordered w-full">

    {{-- Campo oculto para enviar a URL da capa vinda da API --}}
    @if(old('imagem_capa'))
        <input type="hidden" name="imagem_capa" value="{{ old('imagem_capa') }}">
        <img src="{{ old('imagem_capa') }}" alt="Capa sugerida" class="mt-2 w-24 h-32 object-cover border rounded shadow">
        <p class="text-sm text-gray-500 mt-1">Pré-visualização da capa sugerida pela Google Books</p>
    @elseif(isset($livro) && $livro->imagem_capa)
        <img src="{{ asset('storage/'.$livro->imagem_capa) }}" alt="Capa" class="mt-2 w-24 h-32 object-cover">
    @endif
</div>

<div class="flex gap-2 pt-4">
    <button class="btn btn-primary">{{ isset($livro) ? 'Atualizar' : 'Criar' }}</button>
    <a href="{{ route('livros.index') }}" class="btn btn-outline btn-secondary">⬅️ Voltar</a>
</div>
