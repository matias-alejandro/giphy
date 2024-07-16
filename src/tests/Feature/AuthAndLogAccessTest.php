<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AccessLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthAndLogAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Corre las migraciones y seeders
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\PassportPersonalAccessClientSeeder']);
    }

    /** @test */
    public function it_logs_access_for_failed_login_attempt()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseHas('access_logs', [
            'service' => 'api/login',
            'request_body' => json_encode(['email' => 'test@example.com', 'password' => 'password123']),
            'response_status_code' => 401,
        ]);
    }

    /** @test */
    public function it_logs_access_for_successful_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('access_logs', [
            'service' => 'api/login',
            'request_body' => json_encode(['email' => 'test@example.com', 'password' => 'password123']),
            'response_status_code' => 200,
        ]);
    }

    /** @test */
    public function it_logs_access_for_failed_login_invalid_email()
    {
        $user = User::factory()->create([
            'email' => 'real@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'fake@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseHas('access_logs', [
            'service' => 'api/login',
            'request_body' => json_encode(['email' => 'fake@example.com', 'password' => 'password123']),
            'response_status_code' => 401,
        ]);
    }

    /** @test */
    public function it_logs_access_for_failed_login_invalid_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('correctpassword'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseHas('access_logs', [
            'service' => 'api/login',
            'request_body' => json_encode(['email' => 'test@example.com', 'password' => 'wrongpassword']),
            'response_status_code' => 401,
        ]);
    }

}


