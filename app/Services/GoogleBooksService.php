<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Nicebooks\Isbn\IsbnTools;

class GoogleBooksService
{
    protected string $baseUrl = 'https://www.googleapis.com/books/v1/volumes';

    public function byIsbn(string $isbn, ?string $fallbackTitle = null): ?array
    {
        return Cache::remember("gb:isbn:$isbn", now()->addMinutes(15), function () use ($isbn, $fallbackTitle) {
            $isbnTools = new IsbnTools();

            $variantes = [$isbn];

            // Se for ISBN-13 válido, tenta converter para ISBN-10
            if ($isbnTools->isValidIsbn13($isbn)) {
                $variantes[] = $isbnTools->convertIsbn13to10($isbn);
            }

            // Se for ISBN-10 válido, tenta converter para ISBN-13
            if ($isbnTools->isValidIsbn10($isbn)) {
                $variantes[] = $isbnTools->convertIsbn10to13($isbn);
            }

            $variantes = array_unique(array_filter($variantes));

            foreach ($variantes as $isbnTest) {
                $volume = $this->fetchFromApi("isbn:$isbnTest");
                if ($volume) {
                    return $volume;
                }
            }

            // Fallback: tentar por título se fornecido
            if ($fallbackTitle) {
                $volumes = $this->searchByTitle($fallbackTitle, 1);
                if (!empty($volumes)) {
                    return $volumes[0];
                }
            }

            return null;
        });
    }

    public function searchByTitle(string $title, int $maxResults = 5): array
    {
        return $this->fetchFromApi("intitle:$title", $maxResults, false) ?? [];
    }

    protected function fetchFromApi(string $query, int $maxResults = 1, bool $single = true): ?array
    {
        $response = Http::timeout(5)
            ->retry(3, 200)
            ->get($this->baseUrl, [
                'q' => $query,
                'maxResults' => $maxResults,
            ]);

        if (!$response->successful()) {
            return null;
        }

        $items = $response->json('items', []);
        if (empty($items)) {
            return null;
        }

        return $single ? $items[0] : $items;
    }

    public function mapVolumeToLivro(array $volume): array
    {
        $info = $volume['volumeInfo'] ?? [];
        $ids = collect($info['industryIdentifiers'] ?? []);
        $isbn13 = optional($ids->firstWhere('type', 'ISBN_13'))['identifier']
            ?? optional($ids->firstWhere('type', 'ISBN_10'))['identifier']
            ?? null;

        return [
            'isbn'          => $isbn13,
            'nome'          => $info['title'] ?? null,
            'descricao'  => $info['description'] ?? null,
            'imagem_capa'   => $info['imageLinks']['thumbnail'] ?? null,
            'editora_nome'  => $info['publisher'] ?? null,
            'autores_nomes' => collect($info['authors'] ?? [])
                ->flatMap(fn($a) => explode(',', $a)) // separa nomes que vêm juntos
                ->map(fn($a) => trim($a))             // remove espaços
                ->filter()                            // remove vazios
                ->unique()
                ->values()
                ->all(),
        ];
    }
}
