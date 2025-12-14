<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteOrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_prevents_deleting_order_if_it_has_payments(): void
    {
        $user = User::factory()->create();

        $order = Order::factory()->for($user)->create();

        Payment::create([
            'order_id' => $order->id,
            'method' => 'paypal',
            'status' => 'successful',
            'amount' => 100,
        ]);

        $response = $this->actingAs($user, 'api')
            ->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Order cannot be deleted because it has related payments.',
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
        ]);
    }

    /** @test */
    public function it_deletes_order_if_it_has_no_payments(): void
    {
        $user = User::factory()->create();

        $order = Order::factory()->for($user)->create();

        $response = $this->actingAs($user, 'api')
            ->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('orders', [
            'id' => $order->id,
        ]);
    }
}
