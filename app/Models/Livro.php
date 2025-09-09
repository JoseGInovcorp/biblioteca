<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Livro extends Model
{
    protected $fillable = ['isbn', 'nome', 'editora_id', 'descricao', 'imagem_capa', 'preco'];

    protected $casts = [
        'keywords' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (Livro $livro) {
            if ($livro->isDirty('descricao') || empty($livro->keywords)) {
                $livro->keywords = self::extractKeywordsFromDescricao($livro->descricao);
            }
        });
    }

    public static function extractKeywordsFromDescricao(?string $descricao, int $max = 15): array
    {
        if (empty($descricao)) {
            return [];
        }

        // Normalização robusta
        $texto = Str::ascii(strip_tags($descricao));
        $texto = mb_strtolower($texto);
        $texto = preg_replace('/[^a-z\s]/u', ' ', $texto);
        $texto = preg_replace('/\s+/', ' ', $texto);

        $palavras = preg_split('/\s+/', trim($texto)) ?: [];

        $stopwords = [
            'a',
            'o',
            'os',
            'as',
            'um',
            'uma',
            'uns',
            'umas',
            'de',
            'do',
            'da',
            'dos',
            'das',
            'em',
            'no',
            'na',
            'nos',
            'nas',
            'por',
            'para',
            'e',
            'ou',
            'mas',
            'que',
            'se',
            'com',
            'sem',
            'ao',
            'aos',
            'à',
            'às',
            'num',
            'numa',
            'nuns',
            'numas',
            'sobre',
            'entre',
            'como',
            'ser',
            'estar',
            'ter',
            'haver',
            'foi',
            'era',
            'são',
            'é',
            'sua',
            'seu',
            'suas',
            'seus',
            'ela',
            'ele',
            'eles',
            'elas',
            'este',
            'esta',
            'estes',
            'estas',
            'isso',
            'isto',
            'aquele',
            'aquela',
            'aqueles',
            'aquelas',
            'tambem',
            'também',
            'muito',
            'muitos',
            'muita',
            'muitas',
            'pouco',
            'pouca',
            'poucos',
            'poucas',
            'mais',
            'menos',
            'já',
            'ainda',
            'quando',
            'onde',
            'porque',
            'porquê',
            'quem',
            'qual',
            'quais',
            'todo',
            'toda',
            'todos',
            'todas',
            'cada',
            'nessa',
            'nesse',
            'nesta',
            'neste',
            'deste',
            'desta',
            'seja',
            'sejam',
            'sendo',
            'tanto',
            'tão',
            'lhe',
            'lhes',
            'me',
            'te',
            'nos',
            'vos',
            'dela',
            'dele',
            'delas',
            'deles',
        ];

        $filtradas = array_values(array_filter($palavras, function ($w) use ($stopwords) {
            return mb_strlen($w) >= 3 &&
                preg_match('/[aeiou]/', $w) && // exige pelo menos uma vogal
                !in_array($w, $stopwords, true);
        }));

        if (empty($filtradas)) {
            return [];
        }

        $freq = array_count_values($filtradas);
        arsort($freq);
        $top = array_slice(array_keys($freq), 0, $max);

        return array_values($top);
    }

    public function relacionados(int $limit = 5)
    {
        $myKeywords = $this->keywords ?? [];
        $autorIds = $this->autores->pluck('id')->all();

        // Pré-seleção por keywords
        $query = self::query()
            ->where('id', '!=', $this->id)
            ->whereNotNull('keywords');

        $likeTerms = array_slice($myKeywords, 0, 8);
        $query->where(function ($q) use ($likeTerms) {
            foreach ($likeTerms as $kw) {
                $q->orWhere('keywords', 'LIKE', '%' . $kw . '%');
            }
        });

        $candidatos = $query->with(['editora', 'autores'])->take(50)->get();

        // Score por interseção real de keywords
        $scored = $candidatos->map(function ($livro) use ($myKeywords) {
            $score = count(array_intersect($myKeywords, $livro->keywords ?? []));
            return ['livro' => $livro, 'score' => $score];
        })->filter(fn($x) => $x['score'] >= 2);

        $ordenados = $scored->sortByDesc(function ($x) {
            return [$x['score'], optional($x['livro']->created_at)->timestamp];
        })->pluck('livro');

        // Livros do mesmo autor (prioridade máxima)
        $doMesmoAutor = collect();
        if (!empty($autorIds)) {
            $doMesmoAutor = self::whereHas('autores', function ($q) use ($autorIds) {
                $q->whereIn('autores.id', $autorIds);
            })
                ->where('id', '!=', $this->id)
                ->with(['editora', 'autores'])
                ->get();
        }

        // Junta: primeiro mesmo autor, depois conteúdo semelhante
        return $doMesmoAutor
            ->merge($ordenados)
            ->unique('id')
            ->take($limit)
            ->values();
    }

    public function autores()
    {
        return $this->belongsToMany(Autor::class, 'autor_livro', 'livro_id', 'autor_id');
    }

    public function editora()
    {
        return $this->belongsTo(Editora::class);
    }

    public function requisicoes()
    {
        return $this->hasMany(Requisicao::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
