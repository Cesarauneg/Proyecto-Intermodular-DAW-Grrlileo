<?php

namespace Tests\Feature;

use App\Models\Bug;
use App\Models\Fish;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_donate_fish(): void
    {
        $user = User::factory()->create();
        $fish = Fish::create(['file_name' => 'lubina', 'name_es' => 'Lubina', 'name_en' => 'Sea Bass', 'price' => 400, 'location' => 'Mar', 'rarity' => 'Común']);

        $response = $this->actingAs($user)
            ->postJson("/fish/{$fish->id}/donate");

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertTrue(
            $user->fish()->wherePivot('donated_to_museum', true)->where('fish.id', $fish->id)->exists()
        );
    }

    public function test_user_can_toggle_donation_off(): void
    {
        $user = User::factory()->create();
        $fish = Fish::create(['file_name' => 'lubina', 'name_es' => 'Lubina', 'name_en' => 'Sea Bass', 'price' => 400, 'location' => 'Mar', 'rarity' => 'Común']);

        // Donate
        $this->actingAs($user)->postJson("/fish/{$fish->id}/donate");
        // Un-donate (toggle)
        $this->actingAs($user)->postJson("/fish/{$fish->id}/donate");

        $this->assertFalse(
            $user->fish()->where('fish.id', $fish->id)->exists()
        );
    }

    public function test_user_donated_items_list_filters_by_donated(): void
    {
        $user = User::factory()->create();
        $bug1 = Bug::create(['file_name' => 'abeja', 'name_es' => 'Abeja', 'name_en' => 'Bee', 'price' => 200, 'location' => 'Volando', 'rarity' => 'Común']);
        $bug2 = Bug::create(['file_name' => 'mariposa', 'name_es' => 'Mariposa', 'name_en' => 'Butterfly', 'price' => 2500, 'location' => 'Volando', 'rarity' => 'Raro']);

        // Donate only bug1
        $this->actingAs($user)->postJson("/bugs/{$bug1->id}/donate");

        $response = $this->actingAs($user)->getJson('/user/bugs');

        $response->assertOk();
        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertEquals($bug1->id, $data[0]['id']);
    }

    public function test_unauthenticated_user_cannot_donate(): void
    {
        $fish = Fish::create(['file_name' => 'lubina', 'name_es' => 'Lubina', 'name_en' => 'Sea Bass', 'price' => 400, 'location' => 'Mar', 'rarity' => 'Común']);

        $response = $this->postJson("/fish/{$fish->id}/donate");

        $response->assertUnauthorized();
    }
}
