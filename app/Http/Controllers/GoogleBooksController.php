<?php

namespace App\Http\Controllers;

use App\Services\GoogleBooksService;
use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GoogleBooksController extends Controller
{
    public function index()
    {
        return view('google-books.index');
    }

    public function search(Request $request, GoogleBooksService $gb)
    {
        $request->validate([
            'query' => 'required|string|min:3',
            'type'  => 'required|in:isbn,title',
        ]);

        $results = [];
        $fallbackUsed = false;

        if ($request->type === 'isbn') {
            $volume = $gb->byIsbn($request->input('query'), $request->input('query'));

            if ($volume) {
                $mapped = $gb->mapVolumeToLivro($volume);

                // 🔍 Verificar se já existe na BD
                if (!empty($mapped['isbn'])) {
                    $mapped['ja_existe'] = Livro::where('isbn', $mapped['isbn'])->exists();
                } else {
                    $mapped['ja_existe'] = false;
                }

                $results[] = $mapped;

                $ids = $volume['volumeInfo']['industryIdentifiers'] ?? [];
                if (empty($ids)) {
                    $fallbackUsed = true;
                }
            }
        } else {
            $volumes = $gb->searchByTitle($request->input('query'), 5);
            foreach ($volumes as $volume) {
                $mapped = $gb->mapVolumeToLivro($volume);

                if (!empty($mapped['isbn'])) {
                    $mapped['ja_existe'] = Livro::where('isbn', $mapped['isbn'])->exists();
                } else {
                    $mapped['ja_existe'] = false;
                }

                $results[] = $mapped;
            }
        }

        return view('google-books.index', [
            'results'      => $results,
            'query'        => $request->input('query'),
            'type'         => $request->type,
            'fallbackUsed' => $fallbackUsed,
        ]);
    }

    public function prefill(Request $request)
    {
        $request->validate([
            'isbn' => 'required|string',
        ]);

        $volume = (new GoogleBooksService())->byIsbn($request->isbn);

        if (!$volume) {
            return redirect()->back()->with('error', 'Livro não encontrado na API.');
        }

        $dados = (new GoogleBooksService())->mapVolumeToLivro($volume);

        return redirect()
            ->route('livros.create')
            ->withInput($dados);
    }

    /**
     * Pré-preenche o formulário de edição de um livro existente com dados da API
     */
    public function prefillEdit(Livro $livro)
    {
        $volume = (new GoogleBooksService())->byIsbn($livro->isbn);

        if (!$volume) {
            return redirect()
                ->route('livros.edit', $livro)
                ->with('warning', 'Não foi possível obter dados da API. A mostrar dados atuais.');
        }

        $dadosApi = (new GoogleBooksService())->mapVolumeToLivro($volume);

        return redirect()
            ->route('livros.edit', $livro)
            ->withInput($dadosApi)
            ->with('info', 'Dados pré-preenchidos com informações da Google Books. Revise antes de gravar.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'isbn'          => 'required|string',
            'nome'          => 'required|string',
            'descricao'  => 'nullable|string',
            'imagem_capa'   => 'nullable|url',
            'editora_nome'  => 'required_without:nova_editora|nullable|string',
            'nova_editora'  => 'required_without:editora_nome|nullable|string',
            'autores_nomes' => 'array',
            'preco'         => 'nullable|numeric|min:0',
        ]);

        // Se vier nova_editora, cria/associa
        if ($request->filled('nova_editora')) {
            $editora = Editora::firstOrCreate(['nome' => $request->nova_editora]);
        }
        // Caso contrário, usa editora_nome da API ou "Desconhecida"
        else {
            $editora = Editora::firstOrCreate([
                'nome' => $request->editora_nome ?: 'Editora Desconhecida'
            ]);
        }

        // 📷 Capa — gravação consistente com store()/update()
        $caminhoCapa = null;
        if ($request->imagem_capa) {
            try {
                $conteudo = file_get_contents($request->imagem_capa);
                $extensao = pathinfo(parse_url($request->imagem_capa, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $nomeFicheiro = $request->isbn . '.' . $extensao;

                // Guardar usando o mesmo método que o upload manual
                Storage::disk('public')->put('capas/' . $nomeFicheiro, $conteudo);

                $caminhoCapa = 'capas/' . $nomeFicheiro; // caminho relativo para asset('storage/...')
            } catch (\Exception $e) {
                $caminhoCapa = null;
            }
        }


        // Criar ou atualizar livro
        $dadosLivro = [
            'nome'         => $request->nome,
            'descricao' => $request->descricao,
            'preco'        => $request->preco ?? 0.00,
            'editora_id'   => $editora->id,
        ];
        if ($caminhoCapa) {
            $dadosLivro['imagem_capa'] = $caminhoCapa;
        }

        $livro = Livro::updateOrCreate(
            ['isbn' => $request->isbn],
            $dadosLivro
        );

        // Autores
        foreach ($request->autores_nomes ?? [] as $nomeAutor) {
            $nomeLimpo = trim(mb_strtolower($nomeAutor));
            if (empty($nomeLimpo) || is_numeric($nomeLimpo) || !preg_match('/[a-z]/i', $nomeLimpo)) {
                continue;
            }
            $autorExistente = Autor::whereRaw('LOWER(TRIM(nome)) = ?', [$nomeLimpo])->first();
            if (!$autorExistente) {
                $autorExistente = Autor::create(['nome' => $nomeAutor]);
            }
            $livro->autores()->syncWithoutDetaching([$autorExistente->id]);
        }

        return redirect()
            ->route('livros.show', $livro)
            ->with('success', 'Livro importado com sucesso.');
    }
}
