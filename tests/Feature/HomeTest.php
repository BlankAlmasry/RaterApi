<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Client;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;
use Tests\TestCase;

class HomeTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;
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
    public function test_get_home()
    {
        $response = $this->json('get', '/api',[], $this->header);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "description", "title", "links"
        ]);
    }
}
