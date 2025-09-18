<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Livro extends Model
{
    protected $fillable = [
        'isbn',
        'nome',
        'editora_id',
        'descricao',
        'imagem_capa',
        'preco', // legado, se ainda usares
        'stock_venda',
        'preco_venda',
        'preco_requisicao',
        'disponivel_para_requisicao',
        'keywords',
    ];

    protected $casts = [
        'keywords' => 'array',
        'preco_venda' => 'decimal:2',
        'preco_requisicao' => 'decimal:2',
        'disponivel_para_requisicao' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Livro $livro) {
            if ($livro->isDirty('descricao') || empty($livro->keywords)) {
                $livro->keywords = self::extractKeywordsFromDescricao($livro->descricao);
            }
        });
    }

    // Helpers de disponibilidade
    public function isDisponivelParaCompra(): bool
    {
        return ($this->stock_venda ?? 0) > 0;
    }

    public function isDisponivelParaRequisicao(): bool
    {
        return (bool) $this->disponivel_para_requisicao === true;
    }

    // Scopes
    public function scopeDisponiveisParaCompra($query)
    {
        return $query->where('stock_venda', '>', 0);
    }

    public function scopeDisponiveisParaRequisicao($query)
    {
        return $query->where('disponivel_para_requisicao', true);
    }

    /**
     * Lista de stopwords PT para filtrar keywords irrelevantes.
     */
    protected static function stopwordsPt(): array
    {
        return [
            // Artigos, preposições, conjunções
            'a',
            'à',
            'às',
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
            'com',
            'sem',
            'sobre',
            'entre',
            'até',
            'após',
            'antes',
            'desde',
            'contra',
            'perante',
            'trás',
            'sob',
            'e',
            'ou',
            'mas',
            'nem',
            'que',
            'se',
            'como',
            'também',
            'quando',
            'onde',
            'quanto',
            // Pronomes e determinantes
            'eu',
            'tu',
            'ele',
            'ela',
            'nós',
            'vos',
            'eles',
            'elas',
            'me',
            'te',
            'se',
            'nos',
            'vos',
            'lhe',
            'lhes',
            'minha',
            'meu',
            'minhas',
            'meus',
            'tua',
            'teu',
            'tuas',
            'teus',
            'sua',
            'seu',
            'suas',
            'seus',
            'este',
            'esta',
            'isto',
            'aquele',
            'aquela',
            'aquilo',
            'algum',
            'alguma',
            'alguns',
            'algumas',
            'todo',
            'toda',
            'todos',
            'todas',
            'cada',
            'qualquer',
            // Verbos auxiliares e genéricos
            'ser',
            'estar',
            'ter',
            'haver',
            'fazer',
            'poder',
            'dever',
            'querer',
            'dizer',
            'ver',
            'saber',
            'ficar',
            'ir',
            'vir',
            'dar',
            'passar',
            'pode',
            'não',
            'tem',
            'há',
            'foi',
            'era',
            'são',
            'será',
            'vai',
            'vais',
            'vamos',
            'vão',
            'está',
            'estavam',
            'estive',
            'tinha',
            // Termos genéricos de baixo valor semântico
            'vida',
            'história',
            'coisa',
            'caso',
            'tempo',
            'mundo',
            'ano',
            'dia',
            'noite',
            'casa',
            'pessoa',
            'gente',
        ];
    }

    /**
     * Extrai keywords limpas da descrição.
     */
    public static function extractKeywordsFromDescricao(?string $descricao, int $max = 15): array
    {
        if (empty($descricao)) {
            return [];
        }

        $texto = Str::of(strip_tags($descricao))
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9\s]/', ' ')
            ->replaceMatches('/\s+/', ' ')
            ->value();

        $tokens = array_filter(explode(' ', $texto), function ($t) {
            return $t !== '' && !preg_match('/\d/', $t) && strlen($t) >= 4;
        });

        $stops = array_flip(static::stopwordsPt());
        $filtered = array_values(array_filter($tokens, function ($t) use ($stops) {
            return !isset($stops[$t]);
        }));

        if (empty($filtered)) {
            return [];
        }

        $freq = [];
        foreach ($filtered as $t) {
            $freq[$t] = ($freq[$t] ?? 0) + 1;
        }
        arsort($freq);

        return array_slice(array_keys($freq), 0, $max);
    }

    /**
     * Obtém livros relacionados com base em keywords e autores.
     */
    public function relacionados(int $limit = 5)
    {
        $myKeywords = is_array($this->keywords) ? $this->keywords : [];
        $autorIds   = $this->autores->pluck('id')->all();
        $generoIds  = $this->generos->pluck('id')->all();

        $query = self::query()
            ->where('id', '!=', $this->id)
            ->whereNotNull('keywords')
            ->with(['editora', 'autores', 'generos']);

        if (!empty($generoIds)) {
            $query->whereHas('generos', fn($q) => $q->whereIn('generos.id', $generoIds));
        }

        $candidatos = $query->limit(200)->get();

        $mesmoAutor = $candidatos->filter(function ($livro) use ($autorIds) {
            return $livro->autores->pluck('id')->intersect($autorIds)->isNotEmpty();
        });

        $mesmoTema = $candidatos
            ->reject(function ($livro) use ($autorIds) {
                return $livro->autores->pluck('id')->intersect($autorIds)->isNotEmpty();
            })
            ->filter(function ($livro) use ($myKeywords, $generoIds) {
                $k2 = is_array($livro->keywords) ? $livro->keywords : [];
                $overlap = count(array_intersect($myKeywords, $k2));
                $temGeneroEmComum = !empty($generoIds) &&
                    $livro->generos->pluck('id')->intersect($generoIds)->isNotEmpty();
                return $temGeneroEmComum || $overlap >= 3;
            });

        return $mesmoAutor
            ->merge($mesmoTema)
            ->unique('id')
            ->take($limit)
            ->values();
    }

    // Relações
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

    public function generos()
    {
        return $this->belongsToMany(Genero::class, 'genero_livro');
    }

    public function alertas()
    {
        return $this->hasMany(AlertaLivro::class);
    }
}
