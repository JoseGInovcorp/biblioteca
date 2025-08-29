<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Editora;
use App\Models\Autor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LivroController extends Controller
{
    public function index(Request $request)
    {
        $query = Livro::with('editora', 'autores');

        // Pesquisa
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where('nome', 'like', "%{$q}%")
                ->orWhere('isbn', 'like', "%{$q}%");
        }

        // Filtro por editora
        if ($request->filled('editora_id')) {
            $query->where('editora_id', $request->editora_id);
        }

        // Filtro por autor
        if ($request->filled('autor_id')) {
            $query->whereHas('autores', function ($sub) use ($request) {
                $sub->where('autor_id', $request->autor_id);
            });
        }

        // Ordenação
        $sort = $request->get('sort', 'nome');
        $direction = $request->get('direction', 'asc');
        $query->orderBy($sort, $direction);

        $livros = $query->paginate(10)->appends($request->query());

        return view('pages.livros.index', [
            'livros' => $livros,
            'editoras' => Editora::all(),
            'autores' => Autor::all(),
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        return view('pages.livros.create', [
            'editoras' => Editora::all(),
            'autores' => Autor::all(),
        ]);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'isbn' => 'required|string|max:255|unique:livros',
            'editora_id' => 'required|exists:editoras,id',
            'bibliografia' => 'nullable|string',
            'preco' => 'required|numeric',
            'imagem_capa' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'autores' => 'array',
            'autores.*' => 'exists:autores,id',
        ]);

        if ($request->hasFile('imagem_capa')) {
            $validated['imagem_capa'] = $request->file('imagem_capa')->store('capas', 'public');
        }

        $livro = Livro::create($validated);

        if (!empty($validated['autores'])) {
            $livro->autores()->sync($validated['autores']);
        }

        return redirect()->route('livros.index')->with('success', 'Livro criado com sucesso!');
    }

    public function show(Livro $livro)
    {
        $livro->load(['editora', 'autores']);

        if (auth()->user()->isAdmin()) {
            $livro->load(['requisicoes.cidadao']);
        } else {
            $livro->setRelation('requisicoes', $livro->requisicoes()->where('cidadao_id', auth()->id())->with('cidadao')->get());
        }

        return view('pages.livros.show', compact('livro'));
    }


    public function edit(Livro $livro)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        return view('pages.livros.edit', [
            'livro' => $livro,
            'editoras' => Editora::all(),
            'autores' => Autor::all(),
        ]);
    }

    public function update(Request $request, Livro $livro)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'isbn' => 'required|string|max:255|unique:livros,isbn,' . $livro->id,
            'editora_id' => 'required|exists:editoras,id',
            'bibliografia' => 'nullable|string',
            'preco' => 'required|numeric',
            'imagem_capa' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'autores' => 'array',
            'autores.*' => 'exists:autores,id',
        ]);

        if ($request->hasFile('imagem_capa')) {
            if ($livro->imagem_capa) {
                Storage::disk('public')->delete($livro->imagem_capa);
            }
            $validated['imagem_capa'] = $request->file('imagem_capa')->store('capas', 'public');
        }

        $livro->update($validated);

        if (!empty($validated['autores'])) {
            $livro->autores()->sync($validated['autores']);
        }

        return redirect()->route('livros.index')->with('success', 'Livro atualizado com sucesso!');
    }

    public function destroy(Livro $livro)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        if ($livro->imagem_capa) {
            Storage::disk('public')->delete($livro->imagem_capa);
        }
        $livro->delete();

        return redirect()->route('livros.index')->with('success', 'Livro apagado com sucesso!');
    }
}
