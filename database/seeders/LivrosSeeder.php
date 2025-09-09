<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Livro;

class LivrosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $livros = [
            [
                'isbn' => '9789896601234',
                'nome' => 'Ensaio sobre a Cegueira',
                'editora_id' => 1,
                'descricao' => 'Romance de José Saramago publicado em 1995.',
                'imagem_capa' => 'cegueira.jpg',
                'preco' => 14.90,
                'autores' => [1]
            ],
            [
                'isbn' => '9789722521234',
                'nome' => 'O Cavaleiro da Dinamarca',
                'editora_id' => 2,
                'descricao' => 'Obra de Sophia de Mello Breyner Andresen.',
                'imagem_capa' => 'dinamarca.jpg',
                'preco' => 12.50,
                'autores' => [2]
            ],
            [
                'isbn' => '9789722321234',
                'nome' => 'Mensagem',
                'editora_id' => 3,
                'descricao' => 'Único livro publicado por Fernando Pessoa em vida.',
                'imagem_capa' => 'mensagem.jpg',
                'preco' => 10.00,
                'autores' => [3]
            ],
        ];

        foreach ($livros as $livroData) {
            $autores = $livroData['autores'];
            unset($livroData['autores']);

            // Cria o livro com cifragem automática via mutators
            $livro = Livro::create($livroData);

            // Associa os autores usando Eloquent
            $livro->autores()->attach($autores);
        }
    }
}
