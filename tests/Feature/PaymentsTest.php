<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_pay_if_order_not_confirmed(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create([
            'status' => 'pending',
            'total_amount' => 100,
        ]);

        $res = $this->actingAs($user, 'api')->postJson("/api/orders/{$order->id}/payments", [
            'payment_method' => 'paypal',
            'payload' => [],
        ]);

        $res->assertStatus(422);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_returns_422_for_unsupported_payment_method(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create([
            'status' => 'confirmed',
            'total_amount' => 100,
        ]);

        $res = $this->actingAs($user, 'api')->postJson("/api/orders/{$order->id}/payments", [
            'payment_method' => 'unknown_gateway',
            'payload' => [],
        ]);

        $res->assertStatus(422);
    }
}
