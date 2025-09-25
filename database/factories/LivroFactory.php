<?php

namespace Database\Factories;

use App\Models\Livro;
use App\Models\Editora;
use Illuminate\Database\Eloquent\Factories\Factory;

class LivroFactory extends Factory
{
    protected $model = Livro::class;

    public function definition(): array
    {
        return [
            'isbn' => $this->faker->isbn13(),
            'nome' => $this->faker->sentence(3),
            // Cria automaticamente uma Editora válida
            'editora_id' => Editora::factory(),
            'descricao' => $this->faker->paragraphs(2, true),
            'imagem_capa' => $this->faker->imageUrl(200, 300, 'books', true, 'Capa'),
            'preco' => $this->faker->randomFloat(2, 5, 50),
            'stock_venda' => $this->faker->numberBetween(0, 20),
            'preco_venda' => $this->faker->randomFloat(2, 5, 50),
            'preco_requisicao' => $this->faker->randomFloat(2, 1, 10),
            'disponivel_para_requisicao' => $this->faker->boolean(),
            'keywords' => implode(', ', $this->faker->words(5)), // garante string
        ];
    }

    public function semStockVenda(): self
    {
        return $this->state(fn() => ['stock_venda' => 0]);
    }

    public function indisponivelParaRequisicao(): self
    {
        return $this->state(fn() => ['disponivel_para_requisicao' => false]);
    }
}
