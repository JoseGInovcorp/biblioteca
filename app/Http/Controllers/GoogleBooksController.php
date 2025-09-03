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
                $results[] = $gb->mapVolumeToLivro($volume);

                $ids = $volume['volumeInfo']['industryIdentifiers'] ?? [];
                if (empty($ids)) {
                    $fallbackUsed = true;
                }
            }
        } else {
            $volumes = $gb->searchByTitle($request->input('query'), 5);
            foreach ($volumes as $volume) {
                $results[] = $gb->mapVolumeToLivro($volume);
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

    public function import(Request $request)
    {
        $request->validate([
            'isbn'          => 'required|string',
            'nome'          => 'required|string',
            'bibliografia'  => 'nullable|string',
            'imagem_capa'   => 'nullable|url',
            'editora_nome'  => 'nullable|string',
            'autores_nomes' => 'array',
            'preco'         => 'nullable|numeric|min:0',
        ]);

        $editora = Editora::firstOrCreate([
            'nome' => $request->editora_nome ?: 'Editora Desconhecida'
        ]);

        $caminhoCapa = null;
        if ($request->imagem_capa) {
            try {
                $conteudo = file_get_contents($request->imagem_capa);
                $extensao = pathinfo(parse_url($request->imagem_capa, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $ficheiro = 'capas/' . $request->isbn . '.' . $extensao;
                Storage::disk('public')->put($ficheiro, $conteudo);
                $caminhoCapa = $ficheiro;
            } catch (\Exception $e) {
                $caminhoCapa = null;
            }
        }

        $livro = Livro::updateOrCreate(
            ['isbn' => $request->isbn],
            [
                'nome'         => $request->nome,
                'bibliografia' => $request->bibliografia,
                'imagem_capa'  => $caminhoCapa,
                'preco'        => $request->preco ?? 0.00,
                'editora_id'   => $editora->id,
            ]
        );

        foreach ($request->autores_nomes ?? [] as $nomeAutor) {
            $nomeLimpo = trim(mb_strtolower($nomeAutor));

            if (
                empty($nomeLimpo) ||
                is_numeric($nomeLimpo) ||
                !preg_match('/[a-z]/i', $nomeLimpo)
            ) {
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
