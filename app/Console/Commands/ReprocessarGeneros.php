<?php

namespace App\Console\Commands;

use App\Models\Genero;
use App\Models\Livro;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ReprocessarGeneros extends Command
{
    protected $signature = 'generos:reprocessar {--limit=0}';
    protected $description = 'Reprocessa todos os livros aplicando a lógica de géneros multi, tradução PT e fallback por descrição';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        $query = Livro::whereNotNull('isbn')->where('isbn', '!=', '');
        if ($limit > 0) {
            $query->take($limit);
        }

        $query->chunk(50, function ($chunk) {
            foreach ($chunk as $livro) {
                try {
                    $categorias = $this->fetchCategoriesByIsbn($livro->isbn);

                    if (empty($categorias)) {
                        $this->line("Sem categorias para ISBN {$livro->isbn}, tentando fallback…");
                        $fallback = $this->inferCategoryFromDescription($livro->descricao);
                        if ($fallback) {
                            $categorias = [$fallback];
                        }
                    }

                    if (empty($categorias)) {
                        $categorias = ['Outros'];
                    }

                    $categoriasNormalizadas = array_unique(array_map([$this, 'normalizeCategory'], $categorias));

                    $generoIds = [];
                    foreach ($categoriasNormalizadas as $nome) {
                        $genero = Genero::firstOrCreate(['nome' => $nome]);
                        $generoIds[] = $genero->id;
                    }

                    // Aqui substituímos os géneros antigos pelos novos
                    $livro->generos()->sync($generoIds);

                    $this->info("{$livro->nome} -> " . implode(', ', $categoriasNormalizadas));
                } catch (\Throwable $e) {
                    $this->warn("Falhou ISBN {$livro->isbn}: {$e->getMessage()}");
                }
            }
        });

        return self::SUCCESS;
    }

    protected function fetchCategoriesByIsbn(string $isbn): array
    {
        $resp = Http::timeout(8)->get('https://www.googleapis.com/books/v1/volumes', [
            'q' => 'isbn:' . $isbn,
            'maxResults' => 1,
            'printType' => 'books',
        ]);

        if (!$resp->ok()) return [];

        $data = $resp->json();
        $items = $data['items'] ?? [];
        if (empty($items)) return [];

        $info = $items[0]['volumeInfo'] ?? [];
        $categories = $info['categories'] ?? [];

        $final = [];
        foreach ($categories as $cat) {
            foreach (preg_split('/,|\/|>/', $cat) as $part) {
                $part = trim($part);
                if ($part !== '') {
                    $final[] = $part;
                }
            }
        }

        return array_unique($final);
    }

    protected function normalizeCategory(string $cat): string
    {
        $cat = trim(preg_replace('/\s+/', ' ', $cat));

        $map = [
            'Fiction' => 'Ficção',
            'Literary Collections' => 'Coleções Literárias',
            'Authors, Portuguese' => 'Autores Portugueses',
            'Children\'s stories' => 'Infantil',
            'Children' => 'Infantil',
            'Young Adult Fiction' => 'Ficção Juvenil',
            'Biography & Autobiography' => 'Biografia',
            'Education' => 'Educação',
            'Philosophy' => 'Filosofia',
            'Psychology' => 'Psicologia',
            'Self-Help' => 'Autoajuda',
            'Health & Fitness' => 'Saúde e Fitness',
            'Child rearing' => 'Parentalidade',
            'Human behavior' => 'Comportamento Humano',
            'Business ethics' => 'Ética Empresarial',
            'Finance, Personal' => 'Finanças Pessoais',
            'Cooking' => 'Culinária',
            'History' => 'História',
            'Travel' => 'Viagens',
            'Boston (Mass.)' => 'Outros',
            'Carpenters' => 'Outros',
            'Bushido' => 'Filosofia',
        ];

        return $map[$cat] ?? $cat;
    }

    protected function inferCategoryFromDescription(?string $descricao): ?string
    {
        if (!$descricao) return null;

        $descricaoLower = mb_strtolower($descricao, 'UTF-8');

        $keywordsMap = [
            'Romance' => ['amor', 'paixão', 'relacionamento', 'romance'],
            'Thriller' => ['crime', 'assassinato', 'mistério', 'suspense', 'investigação'],
            'Ficção Científica' => ['futuro', 'espaço', 'alienígena', 'robô', 'tecnologia'],
            'Fantasia' => ['magia', 'dragão', 'feiticeiro', 'reino', 'espada'],
            'História' => ['história', 'histórico', 'guerra', 'revolução'],
            'Autoajuda' => ['sucesso', 'motivação', 'felicidade', 'vida', 'propósito'],
            'Biografia' => ['memórias', 'autobiografia', 'vida de', 'história de vida'],
            'Infantil' => ['criança', 'infantil', 'conto', 'fábula'],
            'Saúde e Fitness' => ['dieta', 'exercício', 'saúde', 'alimentação', 'nutrição'],
        ];

        foreach ($keywordsMap as $genero => $palavras) {
            foreach ($palavras as $palavra) {
                if (str_contains($descricaoLower, mb_strtolower($palavra, 'UTF-8'))) {
                    return $genero;
                }
            }
        }

        return null;
    }
}
