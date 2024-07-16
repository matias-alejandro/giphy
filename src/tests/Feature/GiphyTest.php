<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\FavoriteGif;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class GiphyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\PassportPersonalAccessClientSeeder']);
    }

    /** @test */
    public function it_searches_for_gifs()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->getJson('/api/gifs?query=table&limit=5&offset=0');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                    '*' => ['id', 'title', 'url']
                 ]);
    }

    /** @test */
    public function it_fetches_gif_by_id()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $gifId = '3o7527pa7qs9kCG78A';
        $response = $this->getJson("/api/gifs/{$gifId}");

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => ['id', 'title', 'url']]);

        $this->assertDatabaseHas('access_logs', [
            'service' => "api/gifs/{$gifId}",
            'response_status_code' => 200,
        ]);
    }

    /** @test */
    public function it_adds_a_favorite_gif()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $data = [
            'gif_id' => '123abc',
            'alias' => 'un gif',
            'user_id' => $user->id,
        ];

        $response = $this->postJson('/api/gifs/favorite', $data);

        $response->assertStatus(204);

        $this->assertDatabaseHas('favorite_gifs', [
            'gif_id' => '123abc',
            'alias' => 'un gif',
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('access_logs', [
            'service' => 'api/gifs/favorite',
            'request_body' => json_encode($data),
            'response_status_code' => 204,
        ]);
    }
}
