<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Editora;
use App\Models\Autor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'livros'   => $livros,
            'editoras' => Editora::all(),
            'autores'  => Autor::all(),
            'sort'     => $sort,
            'direction' => $direction,
        ]);
    }

    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        $editoras = Editora::all();
        $autores  = Autor::all();

        // Injetar sugestões vindas da API (old input)
        if (session()->hasOldInput()) {
            $editoraNome = old('editora_nome');
            if ($editoraNome && !$editoras->contains('nome', $editoraNome)) {
                $editoras->push(new Editora([
                    'id'   => 'nova_' . Str::slug($editoraNome),
                    'nome' => $editoraNome,
                ]));
            }

            foreach (old('autores_nomes', []) as $nome) {
                if (!$autores->contains('nome', $nome)) {
                    $autores->push(new Autor([
                        'id'   => 'novo_' . Str::slug($nome),
                        'nome' => $nome,
                    ]));
                }
            }
        }

        return view('pages.livros.create', compact('editoras', 'autores'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        // 1) Editora vinda da API (editora_nome)
        if ($request->filled('editora_nome')) {
            $editora = Editora::firstOrCreate(['nome' => $request->editora_nome]);
            $request->merge(['editora_id' => $editora->id]);
        }

        // 2) Nova editora escrita pelo utilizador se não escolheu nenhuma
        if (!$request->filled('editora_id') && $request->filled('nova_editora')) {
            $editora = Editora::firstOrCreate(['nome' => $request->nova_editora]);
            $request->merge(['editora_id' => $editora->id]);
        }

        $validated = $request->validate([
            'nome'        => 'required|string|max:255',
            'isbn'        => 'required|string|max:255|unique:livros',
            'editora_id'  => 'required|exists:editoras,id',
            'bibliografia' => 'nullable|string',
            'preco'       => 'required|numeric',
            // Aceita ficheiro OU URL (fallback da API)
            'imagem_capa' => 'nullable',
            'autores'     => 'array',
            'autores.*'   => 'exists:autores,id',
        ]);

        // Capa: upload manual OU URL da API
        $caminhoCapa = null;

        if ($request->hasFile('imagem_capa')) {
            $caminhoCapa = $request->file('imagem_capa')->store('capas', 'public');
        } elseif ($request->filled('imagem_capa') && filter_var($request->imagem_capa, FILTER_VALIDATE_URL)) {
            try {
                $conteudo  = file_get_contents($request->imagem_capa);
                $extensao  = pathinfo(parse_url($request->imagem_capa, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $ficheiro  = 'capas/' . $request->isbn . '.' . $extensao;
                Storage::disk('public')->put($ficheiro, $conteudo);
                $caminhoCapa = $ficheiro;
            } catch (\Exception $e) {
                // Falha no download: segue sem capa
            }
        }

        if ($caminhoCapa) {
            $validated['imagem_capa'] = $caminhoCapa;
        }

        $livro = Livro::create($validated);

        // Autores: IDs existentes + criação dinâmica via nomes vindos da API
        $autoresIds = $validated['autores'] ?? [];

        foreach ($request->autores_nomes ?? [] as $nomeAutor) {
            $nomeLimpo = trim(mb_strtolower($nomeAutor));
            if (empty($nomeLimpo) || is_numeric($nomeLimpo) || !preg_match('/[a-z]/i', $nomeLimpo)) {
                continue;
            }
            $autor = Autor::firstOrCreate(['nome' => $nomeAutor]);
            $autoresIds[] = $autor->id;
        }

        $livro->autores()->sync($autoresIds);

        return redirect()->route('livros.index')->with('success', 'Livro criado com sucesso!');
    }

    public function show(Livro $livro)
    {
        $livro->load(['editora', 'autores']);

        if (auth()->user()->isAdmin()) {
            $livro->load(['requisicoes.cidadao']);
        } else {
            $livro->setRelation(
                'requisicoes',
                $livro->requisicoes()
                    ->where('cidadao_id', auth()->id())
                    ->with('cidadao')
                    ->get()
            );
        }

        return view('pages.livros.show', compact('livro'));
    }

    public function edit(Livro $livro)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        return view('pages.livros.edit', [
            'livro'    => $livro,
            'editoras' => Editora::all(),
            'autores'  => Autor::all(),
        ]);
    }

    public function update(Request $request, Livro $livro)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        // Nova editora no update (quando utilizador escreve uma nova)
        if (!$request->filled('editora_id') && $request->filled('nova_editora')) {
            $editora = Editora::firstOrCreate(['nome' => $request->nova_editora]);
            $request->merge(['editora_id' => $editora->id]);
        }

        $validated = $request->validate([
            'nome'        => 'required|string|max:255',
            'isbn'        => 'required|string|max:255|unique:livros,isbn,' . $livro->id,
            'editora_id'  => 'required|exists:editoras,id',
            'bibliografia' => 'nullable|string',
            'preco'       => 'required|numeric',
            // Aceita ficheiro OU URL, tal como no store()
            'imagem_capa' => 'nullable',
            'autores'     => 'array',
            'autores.*'   => 'exists:autores,id',
        ]);

        // Capa: upload manual OU URL (substitui a anterior)
        $caminhoCapa = null;

        if ($request->hasFile('imagem_capa')) {
            if ($livro->imagem_capa) {
                Storage::disk('public')->delete($livro->imagem_capa);
            }
            $caminhoCapa = $request->file('imagem_capa')->store('capas', 'public');
        } elseif ($request->filled('imagem_capa') && filter_var($request->imagem_capa, FILTER_VALIDATE_URL)) {
            try {
                $conteudo  = file_get_contents($request->imagem_capa);
                $extensao  = pathinfo(parse_url($request->imagem_capa, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $ficheiro  = 'capas/' . $request->isbn . '.' . $extensao;
                Storage::disk('public')->put($ficheiro, $conteudo);
                if ($livro->imagem_capa) {
                    Storage::disk('public')->delete($livro->imagem_capa);
                }
                $caminhoCapa = $ficheiro;
            } catch (\Exception $e) {
                // Falha no download: mantém capa atual
            }
        }

        if ($caminhoCapa) {
            $validated['imagem_capa'] = $caminhoCapa;
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
