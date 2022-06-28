<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
{
    use WithFaker, RefreshDatabase;
    /**
     * @test
     *
     * @return void
     */
    public function testRegister()
    {
        $jsonData = [
            'name' => $this->faker->words(2, true),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $this->faker->realText(15)
        ];
        $response = $this->postJson('/api/register', $jsonData);
        $response
            ->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $jsonData['name'],
                    'email' => $jsonData['email']
                ]
            ]);
    }

    public function testFailedRegister()
    {
        $jsonData = [
            'name' => $this->faker->words(2, true),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $this->faker->words(5, true)
        ];
        $response = $this->postJson('/api/register', $jsonData);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(['success' => false]);
    }

    public function testLogin()
    {
        $user = User::factory()->create();
        $response = $this->postJson('/api/login', [
            'email' => $user['email'],
            'password' => 'password'
        ]);
        $response
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function testLoginInvalidInput()
    {
        $user = User::factory()->create();
        $response = $this->postJson('/api/login', [
            'email' => $user['email']
        ]);
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(['success' => false]);
    }

    public function testLoginInvalidCredential()
    {
        $user = User::factory()->create();
        $response = $this->postJson('/api/login', [
            'email' => $user['email'],
            'password' => 'test'
        ]);
        $response
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson(['success' => false]);
    }

    public function testLoginErrorOnCreatingToken()
    {
        JWTAuth::shouldReceive('attempt')->andThrow(new JWTException('error'));
        $user = User::factory()->create();
        $response = $this->postJson('/api/login', [
            'email' => $user['email'],
            'password' => 'test'
        ]);
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testGetProfile()
    {
        $user = User::factory()->create();
        $token = JWTAuth::attempt([
            'email' => $user['email'],
            'password' => 'password'
        ]);
        $response = $this->withHeader('Authorization', 'Bearer '.$token)->getJson('/api/me');
        $response
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }
}
