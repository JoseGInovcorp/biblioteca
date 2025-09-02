<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Importar Livros da Google Books</h2>
    </x-slot>

    <div class="p-6">
        {{-- Erros de validação --}}
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-800 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('google-books.search') }}" method="GET" class="mb-6 flex gap-2">
            <select name="type" class="border rounded p-2">
                <option value="isbn" {{ (request('type') === 'isbn') ? 'selected' : '' }}>ISBN</option>
                <option value="title" {{ (request('type') === 'title') ? 'selected' : '' }}>Título</option>
            </select>
            <input type="text" name="query" value="{{ request('query') }}" placeholder="Pesquisar..." class="border rounded p-2 flex-1">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Pesquisar</button>
        </form>

        @isset($results)
            @if(!empty($fallbackUsed) && $fallbackUsed)
                <div class="mb-4 p-3 bg-yellow-100 border border-yellow-300 text-yellow-800 rounded">
                    Nenhum resultado encontrado pelo ISBN. Mostrando resultados por título como alternativa.
                </div>
            @endif

            @if(count($results))
                <table class="table-auto w-full border">
                    <thead>
                        <tr>
                            <th class="border px-2 py-1">Capa</th>
                            <th class="border px-2 py-1">Título</th>
                            <th class="border px-2 py-1">Autores</th>
                            <th class="border px-2 py-1">Editora</th>
                            <th class="border px-2 py-1">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $livro)
                            <tr>
                                <td class="border px-2 py-1">
                                    @if(!empty($livro['imagem_capa']))
                                        <img src="{{ $livro['imagem_capa'] }}" alt="Capa" class="h-20">
                                    @endif
                                </td>
                                <td class="border px-2 py-1">{{ $livro['nome'] ?? '' }}</td>
                                <td class="border px-2 py-1">{{ !empty($livro['autores_nomes']) ? implode(', ', $livro['autores_nomes']) : '' }}</td>
                                <td class="border px-2 py-1">{{ $livro['editora_nome'] ?? '' }}</td>
                                <td class="border px-2 py-1">
                                    @if(!empty($livro['isbn']))
                                        <form action="{{ route('google-books.import') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="isbn" value="{{ $livro['isbn'] }}">
                                            <input type="hidden" name="nome" value="{{ $livro['nome'] }}">
                                            <input type="hidden" name="bibliografia" value="{{ $livro['bibliografia'] }}">
                                            <input type="hidden" name="imagem_capa" value="{{ $livro['imagem_capa'] }}">
                                            <input type="hidden" name="editora_nome" value="{{ $livro['editora_nome'] }}">
                                            @foreach(($livro['autores_nomes'] ?? []) as $autor)
                                                <input type="hidden" name="autores_nomes[]" value="{{ $autor }}">
                                            @endforeach
                                            <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded">Importar</button>
                                        </form>
                                    @else
                                        <span class="text-gray-500 italic">Sem ISBN — não é possível importar</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>Nenhum resultado encontrado.</p>
            @endif
        @endisset
    </div>
</x-app-layout>
