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
            'gente'
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
            return $t !== '' && !preg_match('/\d/', $t) && mb_strlen($t) >= 4;
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
        $myKeywords = $this->keywords ?? [];
        $autorIds   = $this->autores->pluck('id')->all();
        $generoIds  = $this->generos->pluck('id')->all();

        // Base de candidatos: todos os outros livros com keywords
        $candidatos = self::query()
            ->where('id', '!=', $this->id)
            ->whereNotNull('keywords')
            ->with(['editora', 'autores', 'generos'])
            ->get();

        // Grupo: mesmo autor
        $mesmoAutor = $candidatos->filter(function ($livro) use ($autorIds) {
            return $livro->autores->pluck('id')->intersect($autorIds)->isNotEmpty();
        });

        // Grupo: semelhantes no tema
        $mesmoTema = $candidatos
            ->reject(function ($livro) use ($autorIds) {
                // exclui os do mesmo autor
                return $livro->autores->pluck('id')->intersect($autorIds)->isNotEmpty();
            })
            ->filter(function ($livro) use ($myKeywords, $generoIds) {
                $overlap = count(array_intersect($myKeywords, $livro->keywords ?? []));
                $temGeneroEmComum = !empty($generoIds) &&
                    $livro->generos->pluck('id')->intersect($generoIds)->isNotEmpty();

                // Critério: ou partilha género, ou tem keywords suficientes em comum
                return $temGeneroEmComum || $overlap >= 3;
            });

        return $mesmoAutor
            ->merge($mesmoTema)
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

    public function generos()
    {
        return $this->belongsToMany(Genero::class, 'genero_livro');
    }
}
