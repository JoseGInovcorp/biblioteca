<?php

use App\Models\User;
use App\Models\Livro;
use App\Models\Requisicao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\AbstractPaginator;

uses(RefreshDatabase::class);

// 1. Criação de requisição de livro
test('um utilizador pode criar uma requisicao de um livro', function () {
    $user = User::factory()->create(['role' => 'cidadao']);
    $livro = Livro::factory()->create(['stock_venda' => 3]);

    $this->actingAs($user);

    $payload = ['livro_id' => $livro->id];

    $response = $this->post(route('requisicoes.store'), $payload);

    $response->assertStatus(302);
    $this->assertDatabaseHas('requisicoes', [
        'cidadao_id' => $user->id,
        'livro_id' => $livro->id,
        'status' => 'ativa',
    ]);
});

// 2. Validação: sem livro válido
test('nao pode criar requisicao sem livro valido', function () {
    $user = User::factory()->create(['role' => 'cidadao']);
    $this->actingAs($user);

    $response = $this->post(route('requisicoes.store'), []);
    $response->assertSessionHasErrors(['livro_id']);

    $response = $this->post(route('requisicoes.store'), ['livro_id' => 999999]);
    $response->assertSessionHasErrors(['livro_id']);
});

// 3. Devolução de livro
test('um utilizador pode devolver um livro', function () {
    $user = User::factory()->create(['role' => 'cidadao']);
    $livro = Livro::factory()->create();
    $requisicao = Requisicao::factory()->create([
        'cidadao_id' => $user->id,
        'livro_id' => $livro->id,
        'status' => 'ativa',
        'data_fim_real' => null,
    ]);

    $this->actingAs($user);

    $response = $this->patch(route('requisicoes.devolver', $requisicao->id));

    $response->assertStatus(302);

    $this->assertDatabaseHas('requisicoes', [
        'id' => $requisicao->id,
        'status' => 'entregue',
    ]);

    expect(Requisicao::find($requisicao->id)->data_fim_real)->not()->toBeNull();
});

// 4. Listagem por utilizador
test('um utilizador ve apenas as suas requisicoes', function () {
    $user = User::factory()->create(['role' => 'cidadao']);
    $outro = User::factory()->create(['role' => 'cidadao']);

    $livroA = Livro::factory()->create();
    $livroB = Livro::factory()->create();

    $r1 = Requisicao::factory()->create(['cidadao_id' => $user->id, 'livro_id' => $livroA->id]);
    $r2 = Requisicao::factory()->create(['cidadao_id' => $user->id, 'livro_id' => $livroB->id]);
    $r3 = Requisicao::factory()->create(['cidadao_id' => $outro->id, 'livro_id' => $livroA->id]);

    $this->actingAs($user);

    $response = $this->get(route('requisicoes.index'));
    $response->assertStatus(200);

    $data = $response->viewData('requisicoes') ?? $response->json('data');
    $ids = collect($data instanceof AbstractPaginator ? $data->items() : $data)->pluck('id');

    expect($ids)->toContain($r1->id, $r2->id)
        ->not()->toContain($r3->id);
});

// 5. Stock na requisição
test('nao e possivel requisitar livro sem stock disponivel', function () {
    $user = User::factory()->create(['role' => 'cidadao']);
    $livro = Livro::factory()->create(['stock_venda' => 0]);

    $this->actingAs($user);

    $response = $this->post(route('requisicoes.store'), [
        'livro_id' => $livro->id,
    ]);

    $response->assertSessionHasErrors(['livro_id']);

    $this->assertDatabaseMissing('requisicoes', [
        'cidadao_id' => $user->id,
        'livro_id' => $livro->id,
    ]);
});
