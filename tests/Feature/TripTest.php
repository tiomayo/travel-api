<?php

namespace Tests\Feature;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Date;

class TripTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function testFetchTrip()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/api/trip');

        $response->assertStatus(200);
    }

    public function testCreateTrip()
    {
        $user = User::factory()->create();
        $trip = [
            'title' => $this->faker->words(2, true),
            'origin' => $this->faker->city(),
            'destination' => $this->faker->city(),
            'start' => Date::now()->format('Y-m-d'),
            'end' => Date::now()->format('Y-m-d'),
            'type' => $this->faker->text(10),
            'description' => $this->faker->words(3, true),
        ];
        $response = $this->actingAs($user)->postJson('/api/trip', $trip);
        $response
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function testCreateTripError()
    {
        $user = User::factory()->create();
        $trip = [
            'title' => $this->faker->words(3, true),
            'origin' => $this->faker->city(),
            'destination' => $this->faker->city(),
            'start' => $this->faker->date(),
            'end' => $this->faker->date(),
        ];
        $response = $this->actingAs($user)->postJson('/api/trip', $trip);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function testUpdateTrip()
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->create(['user_id' => $user->id]);
        $updated = $trip->toArray();
        $updated['title'] = 'title update';
        $response = $this->actingAs($user)->putJson('/api/trip/' . $trip->id, $updated);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => 'title update',
                ]
            ]);
    }

    public function testUpdateTripErrorNotFound()
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->create(['user_id' => $user->id]);
        $updated = $trip->toArray();
        $updated['end'] = Date::now()->subDay()->format('Y-m-d');
        $response = $this->actingAs($user)->putJson('/api/trip/1', $updated);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testUpdateTripError()
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->create(['user_id' => $user->id]);
        $updated = $trip->toArray();
        $updated['end'] = Date::now()->subDay()->format('Y-m-d');
        $response = $this->actingAs($user)->putJson('/api/trip/' . $trip->id, $updated);

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false
            ]);
    }

    public function testDeleteTrip()
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->delete('/api/trip/' . $trip->id);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function testDeleteTripError()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete('/api/trip/1');

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
