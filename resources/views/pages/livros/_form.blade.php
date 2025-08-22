@csrf
@if(isset($livro))
    @method('PUT')
@endif

<div>
    <label class="label">Nome</label>
    <input type="text" name="nome" value="{{ old('nome', $livro->nome ?? '') }}" class="input input-bordered w-full" required>
</div>

<div>
    <label class="label">ISBN</label>
    <input type="text" name="isbn" value="{{ old('isbn', $livro->isbn ?? '') }}" class="input input-bordered w-full" required>
</div>

<div>
    <label class="label">Editora</label>
    <select name="editora_id" class="select select-bordered w-full" required>
        <option value="">Selecione</option>
        @foreach($editoras as $editora)
            <option value="{{ $editora->id }}" @selected(old('editora_id', $livro->editora_id ?? '') == $editora->id)>{{ $editora->nome }}</option>
        @endforeach
    </select>
</div>

<div>
    <label class="label">Autores</label>
    <select name="autores[]" multiple id="autores-select" class="tom-select w-full">
        @foreach($autores->unique('id') as $autor)
            <option value="{{ $autor->id }}" @selected(isset($livro) && $livro->autores->contains($autor->id))>
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
    @if(isset($livro) && $livro->imagem_capa)
        <img src="{{ asset('storage/'.$livro->imagem_capa) }}" alt="Capa" class="mt-2 w-24 h-32 object-cover">
    @endif
</div>

<div class="flex gap-2 pt-4">
    <button class="btn btn-primary">{{ isset($livro) ? 'Atualizar' : 'Criar' }}</button>
    <a href="{{ route('livros.index') }}" class="btn btn-outline btn-secondary">⬅️ Voltar</a>
</div>