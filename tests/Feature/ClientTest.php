<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\MatchUp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Laravel\Passport\Client;
use Tests\TestCase;
use function PHPUnit\Framework\assertInstanceOf;

class ClientTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = Client::factory()->create();

    }

    /** @test */
    public function Client_can_have_games()
    {
        Game::factory()->create(["client_id" => $this->client->getKey()]);
        assertInstanceOf( Game::class, $this->client->games()->first());
    }

    /** @test */
    public function Client_can_have_users()
    {
        User::factory()->create(["client_id" => $this->client->getKey()]);
        assertInstanceOf( User::class, $this->client->users()->first());
    }

    /** @test */
    public function Client_can_have_matches_through_having_games()
    {
        $game = Game::factory()->create(["client_id" => $this->client->getKey()]);
        MatchUp::factory()->count(10)->create(['game_id' => $game->id]);
        assertInstanceOf( MatchUp::class, $this->client->gameMatches()->first());
        assertInstanceOf( Collection::class, $this->client->gameMatches()->get());
    }

}
