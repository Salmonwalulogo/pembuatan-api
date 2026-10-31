<?php

namespace Tests\Feature;

use App\Events\OrderPlaced;
use App\Models\Product;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrderPlacedBroadcastTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_cashier_transaction_dispatches_a_sanitized_broadcast_event(): void
    {
        Event::fake([OrderPlaced::class]);

        $cashier = User::factory()->create();
        $cashier->forceFill(['role' => 'cashier'])->save();
        $product = Product::create([
            'name' => 'Kopi',
            'product_code' => 'PRD-001',
            'category' => 'Minuman',
            'price' => 10000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $response = $this->actingAs($cashier)->postJson(route('cashier.store'), [
            'items' => [['product_id' => $product->id, 'qty' => 2]],
            'discount_percent' => 0,
            'discount_nominal' => 0,
            'tax' => 0,
            'other_fee' => 0,
            'paid_amount' => 20000,
            'payment_method' => 'cash',
            'customer_name' => 'Data pelanggan rahasia',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);

        Event::assertDispatched(OrderPlaced::class, function (OrderPlaced $event) use ($cashier, $response): bool {
            $this->assertSame([
                'transaction_number' => $response->json('transaction.transaction_number'),
                'grand_total' => 20000,
                'payment_method' => 'cash',
                'cashier_name' => $cashier->name,
            ], $event->broadcastWith());

            return true;
        });
    }

    public function test_order_event_uses_a_private_channel_and_does_not_expose_customer_data(): void
    {
        $event = new OrderPlaced('TRX-20261031-001', 20000, 'cash', 'Kasir');

        $this->assertInstanceOf(PrivateChannel::class, $event->broadcastOn()[0]);
        $this->assertSame('private-transactions', $event->broadcastOn()[0]->name);
        $this->assertSame([
            'transaction_number' => 'TRX-20261031-001',
            'grand_total' => 20000,
            'payment_method' => 'cash',
            'cashier_name' => 'Kasir',
        ], $event->broadcastWith());
    }

    public function test_admin_can_subscribe_to_transaction_notifications(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $subscription = [
            'socket_id' => '123.456',
            'channel_name' => 'private-transactions',
        ];

        $this->actingAs($admin)
            ->postJson('/broadcasting/auth', $subscription)
            ->assertOk();
    }

    public function test_cashier_cannot_subscribe_to_transaction_notifications(): void
    {
        $cashier = User::factory()->create();
        $cashier->forceFill(['role' => 'cashier'])->save();

        $response = $this->actingAs($cashier)
            ->postJson('/broadcasting/auth', [
                'socket_id' => '123.456',
                'channel_name' => 'private-transactions',
            ]);

        $this->assertSame(403, $response->status(), $response->getContent());
    }
}
