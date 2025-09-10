<?php

namespace App\Console\Commands;

use App\Models\Genero;
use App\Models\Livro;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class BackfillGenerosFromGoogleBooks extends Command
{
    protected $signature = 'livros:backfill-generos {--limit=500}';
    protected $description = 'Preenche genero_id dos livros usando Google Books API (volumeInfo.categories)';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        Livro::whereNull('genero_id')
            ->whereNotNull('isbn')
            ->where('isbn', '!=', '')
            ->take($limit)
            ->chunk(50, function ($chunk) {
                foreach ($chunk as $livro) {
                    try {
                        $categoria = $this->fetchPrimaryCategoryByIsbn($livro->isbn);

                        if (!$categoria) {
                            $this->line("Sem categoria para ISBN {$livro->isbn}");
                            continue;
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
            'langRestrict' => 'pt' // opcional
        ]);

        if (!$resp->ok()) return null;

        $data = $resp->json();
        $items = $data['items'] ?? [];
        if (empty($items)) return null;

        $info = $items[0]['volumeInfo'] ?? [];
        $categories = $info['categories'] ?? [];
        if (empty($categories)) return null;

        // Pega a última categoria (mais específica) e separa se vier com vírgula
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
            // Ficção e literatura
            'Fiction' => 'Ficção',
            'Literary Collections' => 'Coleções Literárias',
            'Authors, Portuguese' => 'Autores Portugueses',
            'Children\'s stories' => 'Infantil',
            'Children' => 'Infantil',
            'Young Adult Fiction' => 'Ficção Juvenil',

            // Não-ficção
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

            // Casos absurdos ou geográficos
            'Boston (Mass.)' => 'Outros',
            'Carpenters' => 'Outros',
        ];

        return $map[$cat] ?? $cat;
    }
}
