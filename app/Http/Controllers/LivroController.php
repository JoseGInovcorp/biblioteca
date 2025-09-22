<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Editora;
use App\Models\Autor;
use App\Models\Genero;
use App\Mail\LivroDisponivelMail;
use App\Traits\RegistaLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class LivroController extends Controller
{
    use RegistaLog;

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
        $generos  = Genero::orderBy('nome')->get();

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

        // Passar um Livro vazio para o _form não rebentar
        $livro = new Livro();

        return view('pages.livros.create', compact('livro', 'editoras', 'autores', 'generos'));
    }


    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        // 🔍 Verificar se já existe um livro com este ISBN
        if ($request->filled('isbn')) {
            $livroExistente = Livro::where('isbn', $request->isbn)->first();
            if ($livroExistente) {
                return redirect()
                    ->route('livros.edit', $livroExistente)
                    ->with('warning', 'Este livro já existe. Foi redirecionado para a página de edição.');
            }
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
            'nome'         => 'required|string|max:255',
            'isbn'         => 'required|string|max:255',
            'editora_id'   => 'required|exists:editoras,id',
            'descricao'    => 'nullable|string',
            'preco'        => 'required|numeric',
            'imagem_capa'  => 'nullable',
            'autores'      => 'array',
            'stock_venda'  => 'required|integer|min:0',
            'preco_venda'  => 'required|numeric|min:0',
        ]);

        // 📷 Capa: upload manual OU URL da API
        $caminhoCapa = null;

        if ($request->hasFile('imagem_capa')) {
            $caminhoCapa = $request->file('imagem_capa')->store('capas', 'public');
        } elseif ($request->filled('imagem_capa') && filter_var($request->imagem_capa, FILTER_VALIDATE_URL)) {
            try {
                $conteudo = file_get_contents($request->imagem_capa);
                $extensao = pathinfo(parse_url($request->imagem_capa, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $ficheiro = 'capas/' . $request->isbn . '.' . $extensao;
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

        // ✍️ Autores: IDs existentes + criação dinâmica via nomes vindos da API
        $autoresIds = [];

        // 1) Autores selecionados no <select>
        foreach ($request->autores ?? [] as $autorId) {
            if (Str::startsWith($autorId, 'novo_')) {
                $nome = Str::replaceFirst('novo_', '', $autorId);
                $nome = Str::of($nome)->replace('-', ' ')->title();
                $autor = Autor::firstOrCreate(['nome' => $nome]);
                $autoresIds[] = $autor->id;
            } else {
                $autoresIds[] = $autorId;
            }
        }

        // 2) Autores vindos da API (autores_nomes[])
        foreach ($request->autores_nomes ?? [] as $nomeAutor) {
            $nomeLimpo = trim(mb_strtolower($nomeAutor));
            if (empty($nomeLimpo) || is_numeric($nomeLimpo) || !preg_match('/[a-z]/i', $nomeLimpo)) {
                continue;
            }
            $autor = Autor::firstOrCreate(['nome' => $nomeAutor]);
            $autoresIds[] = $autor->id;
        }

        $livro->autores()->sync($autoresIds);

        // 📜 Registar log da criação
        $this->registarLog('Livros', $livro->id, 'Criou um novo livro');

        return redirect()->route('livros.index')->with('success', 'Livro criado com sucesso!');
    }

    public function show(Livro $livro)
    {
        // Carregar editora, autores e reviews ativas com o autor (user)
        $livro->load([
            'editora',
            'autores',
            'generos',
            'reviews' => function ($query) {
                $query->where('estado', 'ativo')->with('user');
            }
        ]);

        // Carregar requisições conforme o tipo de utilizador
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

        // Carregar livros relacionados com base nas keywords
        $relacionados = $livro->relacionados(5);

        return view('pages.livros.show', compact('livro', 'relacionados'));
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
            'generos'  => Genero::orderBy('nome')->get(),
        ]);
    }

    public function update(Request $request, Livro $livro)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        // Fluxo 1: atualização parcial (inline) — apenas preço(s) e stock
        if ($request->boolean('inline')) {
            // validação mínima para o inline
            $validated = $request->validate([
                'preco'        => 'required|numeric|min:0',
                'preco_venda'  => 'required|numeric|min:0',
                'stock_venda'  => 'required|integer|min:0',
            ]);

            $livro->update($validated);

            // 📜 Registar log da atualização inline
            $this->registarLog('Livros', $livro->id, 'Atualizou preço/stock (edição inline)');

            return redirect()
                ->route('livros.index', ['page' => $request->input('page', 1)])
                ->with('success', 'Preço e stock atualizados com sucesso.');
        }

        // Fluxo 2: atualização completa (edit.blade)
        if ($request->filled('nova_editora')) {
            $editora = Editora::firstOrCreate(['nome' => $request->nova_editora]);
            $request->merge(['editora_id' => $editora->id]);
        }

        if ($request->filled('editora_nome')) {
            $editora = Editora::firstOrCreate(['nome' => $request->editora_nome]);
            $request->merge(['editora_id' => $editora->id]);
        }

        if ($request->filled('novo_genero')) {
            $novoGenero = Genero::firstOrCreate(['nome' => $request->novo_genero]);
            $generosIds = $request->input('generos', []);
            $generosIds[] = $novoGenero->id;
            $request->merge(['generos' => $generosIds]);
        }

        $validated = $request->validate([
            'nome'         => 'required|string|max:255',
            'isbn'         => 'required|string|max:255|unique:livros,isbn,' . $livro->id,
            'editora_id'   => 'required_without:nova_editora|exists:editoras,id',
            'nova_editora' => 'required_without:editora_id|string|nullable',
            'descricao'    => 'nullable|string',
            'preco'        => 'required|numeric|min:0',
            'imagem_capa'  => 'nullable',
            'autores'      => 'array',
            'autores.*'    => 'exists:autores,id',
            'generos'      => 'array',
            'generos.*'    => 'exists:generos,id',
            'stock_venda'  => 'required|integer|min:0',
            'preco_venda'  => 'required|numeric|min:0',
        ]);

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

        if (!empty($validated['generos'])) {
            $livro->generos()->sync($validated['generos']);
        }

        $ficouDisponivel = $livro->requisicoes()->where('status', 'ativa')->count() === 0;

        if ($ficouDisponivel) {
            Log::info("📡 Verificação de alertas iniciada para livro {$livro->id}");

            foreach ($livro->alertas()->whereNull('notificado_em')->get() as $alerta) {
                try {
                    Mail::to($alerta->user->email)->send(new LivroDisponivelMail($livro, $alerta));
                    $alerta->update(['notificado_em' => now()]);
                    Log::info("📧 Alerta enviado para {$alerta->user->email}");
                } catch (\Exception $e) {
                    Log::error("❌ Erro ao enviar alerta: " . $e->getMessage());
                }
            }
        }

        // 📜 Registar log da atualização completa
        $this->registarLog('Livros', $livro->id, 'Atualizou informações do livro');

        return redirect()
            ->route('livros.index', ['page' => $request->input('page', 1)])
            ->with('success', 'Livro atualizado com sucesso.');
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

        // 📜 Registar log da eliminação
        $this->registarLog('Livros', $livro->id, 'Apagou o livro');

        return redirect()
            ->route('livros.index')
            ->with('success', 'Livro apagado com sucesso!');
    }
}
