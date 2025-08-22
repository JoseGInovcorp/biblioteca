<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Autor;

class AutoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $autores = [
            ['nome' => 'José Saramago', 'foto' => 'saramago.jpg'],
            ['nome' => 'Sophia de Mello Breyner', 'foto' => 'sophia.jpg'],
            ['nome' => 'Fernando Pessoa', 'foto' => 'pessoa.jpg'],
        ];

        foreach ($autores as $autor) {
            Autor::create($autor);
        }
    }
}
