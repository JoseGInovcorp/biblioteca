<?php

namespace App\Console\Commands;

use App\Models\Genero;
use App\Models\Livro;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateGenerosFromGoogleBooks extends Command
{
    protected $signature = 'livros:update-generos {--limit=200}';
    protected $description = 'Atualiza genero_id de todos os livros usando Google Books API e fallback por descrição';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        Livro::whereNotNull('isbn')
            ->where('isbn', '!=', '')
            ->take($limit)
            ->chunk(50, function ($chunk) {
                foreach ($chunk as $livro) {
                    try {
                        $categoria = $this->fetchPrimaryCategoryByIsbn($livro->isbn);

                        if (!$categoria) {
                            $this->line("Sem categoria para ISBN {$livro->isbn}, tentando fallback…");
                            $categoria = $this->inferCategoryFromDescription($livro->descricao);
                        }

                        if (!$categoria) {
                            $categoria = 'Outros';
                        }

                        $nome = $this->normalizeCategory($categoria);
                        $genero = Genero::firstOrCreate(['nome' => $nome]);

                        $livro->genero()->associate($genero);
                        $livro->saveQuietly();

                        $this->info("{$livro->nome} -> {$genero->nome}");
                    } catch (\Throwable $e) {
                        $this->warn("Falhou ISBN {$livro->isbn}: {$e->getMessage()}");
                    }
                }
            });

        return self::SUCCESS;
    }

    protected function fetchPrimaryCategoryByIsbn(string $isbn): ?string
    {
        $resp = Http::timeout(8)->get('https://www.googleapis.com/books/v1/volumes', [
            'q' => 'isbn:' . $isbn,
            'maxResults' => 1,
            'printType' => 'books',
            'langRestrict' => 'pt'
        ]);

        if (!$resp->ok()) return null;

        $data = $resp->json();
        $items = $data['items'] ?? [];
        if (empty($items)) return null;

        $info = $items[0]['volumeInfo'] ?? [];
        $categories = $info['categories'] ?? [];
        if (empty($categories)) return null;

        $primary = end($categories);
        if (is_string($primary) && str_contains($primary, ',')) {
            $parts = array_map('trim', explode(',', $primary));
            $primary = end($parts);
        }

        return is_string($primary) ? trim($primary) : null;
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
            'Outros' => 'Outros',
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
