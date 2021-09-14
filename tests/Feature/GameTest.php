<?php

namespace Tests\Feature;

use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp(); //
        $this->client = Client::factory()->create();

        $response = $this->post('/api/login', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->client->getKey(),
            'client_secret' => $this->client->secret,
        ]);
        $this->header =  ['Authorization' => 'Bearer '.$response->json()["access_token"]];
    }

    public function test_shows_all_games()
    {
        $this->withoutExceptionHandling();

        Game::factory()->count(10)->create(['client_id'=> $this->client->id]);
        $response = $this->json('get','/api/games',[],$this->header);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "data","meta","links"
        ]);
    }

    public function test_shows_a_single_game()
    {
        Game::factory()->create(["client_id" => $this->client->getKey()]);
        $game = Game::first();
        $response = $this->json('get','/api/games/'.$game->slug,[],$this->header);
        $response->assertStatus(200);
        $response->assertJson([
            'name' => $game->name,
        ]);
    }

    public function test_an_api_can_create_a_game()
    {
        $response = $this->json('post','/api/games',[
            'name' => 'League of Legends',
            'ranked_system' => 'numbered',
        ],$this->header);
        $response->assertStatus(201)->assertJsonFragment(['name' => 'League of Legends']);
    }

    public function test_an_api_can_patch_game_name_system()
    {
        $game = Game::factory()->create(["client_id" => $this->client->getKey()]);

        $response = $this->json('patch','/api/games/' . $game->slug,[
            'name' => 'League of Legends',
        ],$this->header);
        $response->assertStatus(200);

        $game = Game::find(1)->fresh();

        $response->assertJsonFragment([
            'name' => $game->fresh()->name,
        ]);
        $this->assertEquals($game->name ,'League of Legends');
    }

    public function test_an_invalid_patch_request_gets_validation_error()
    {
        $game = Game::factory()->create(["client_id" => $this->client->getKey()]);
        $response = $this->json('patch','/api/games/' . $game->slug,[
            'name' => '',
        ],$this->header);
        $response->assertStatus(400);
    }
    public function test_delete_a_Game()
    {
        $game = Game::factory()->create(["client_id" => $this->client->getKey()]);

        $this->json('delete', "/api/games/{$game->slug}",[], $this->header);

        $response = $this->json('get', "/api/games/{$game->slug}",[], $this->header);

        $response->assertStatus(404);

    }
    /** @test */
    public function User_must_have_a_client()
    {
        $game = Game::factory()->create();
        self::assertInstanceOf(Client::class ,$game->client);
    }

    /** @test */

    public function game_name_must_be_a_string()
    {
        $response = $this->json('post','/api/games' ,[
            'name' => 12131,
        ],$this->header);
        $response->assertStatus(400);
    }

    /** @test */
    public function game_name_cannot_be_empty()
    {
        $response = $this->json('post','/api/games' ,[
            'name' => '',
        ],$this->header);
        $response->assertStatus(400);
    }



}
