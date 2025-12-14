<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_order_calculates_total_amount(): void
    {
        $user = User::factory()->create();

        $res = $this->actingAs($user, 'api')->postJson('/api/orders', [
            'items' => [
                ['product_name' => 'A', 'quantity' => 2, 'price' => 10.5],
                ['product_name' => 'B', 'quantity' => 1, 'price' => 5],
            ],
        ]);

        $res->assertCreated();
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total_amount' => 26.00,
        ]);
    }

    public function test_cannot_delete_order_with_payments(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create();
        Payment::create([
            'order_id' => $order->id,
            'method' => 'paypal',
            'status' => 'successful',
            'amount' => 10,
        ]);

        $res = $this->actingAs($user, 'api')->deleteJson("/api/orders/{$order->id}");
        $res->assertStatus(422);
    }
}
