<?php

namespace Database\Factories;

use App\Models\Requisicao;
use App\Models\User;
use App\Models\Livro;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequisicaoFactory extends Factory
{
    protected $model = Requisicao::class;

    public function definition(): array
    {
        return [
            'livro_id' => Livro::factory(),
            'cidadao_id' => User::factory(),
            'foto_cidadao' => $this->faker->imageUrl(200, 200, 'people', true, 'Foto'),
            'data_inicio' => now(),
            'data_fim_prevista' => now()->addDays(7),
            'data_fim_real' => null,
            'status' => 'ativa',
            'numero_sequencial' => $this->faker->unique()->numberBetween(1, 9999),
        ];
    }

    /**
     * Estado para requisição devolvida.
     */
    public function devolvida(): self
    {
        return $this->state(fn() => [
            'status' => 'devolvida',
            'data_fim_real' => now(),
        ]);
    }

    /**
     * Estado para requisição sem foto de cidadão.
     */
    public function semFoto(): self
    {
        return $this->state(fn() => [
            'foto_cidadao' => null,
        ]);
    }
}
