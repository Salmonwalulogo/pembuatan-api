<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicePdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_download_a_pdf_invoice_for_a_transaction(): void
    {
        $cashier = User::factory()->create();
        $cashier->forceFill(['role' => 'cashier'])->save();

        $transaction = Transaction::create([
            'transaction_number' => 'TRX-20261025-001',
            'user_id' => $cashier->id,
            'customer_name' => 'Pelanggan Demo',
            'subtotal' => 10000,
            'discount' => 1000,
            'tax' => 500,
            'other_fee' => 0,
            'grand_total' => 9500,
            'paid_amount' => 10000,
            'change_amount' => 500,
            'payment_method' => 'cash',
            'status' => 'paid',
        ]);
        $transaction->details()->create([
            'product_name' => 'Produk Demo',
            'price' => 5000,
            'qty' => 2,
            'subtotal' => 10000,
        ]);

        $response = $this->actingAs($cashier)
            ->get(route('cashier.invoice', $transaction));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader(
                'content-disposition',
                'attachment; filename=invoice-TRX-20261025-001.pdf'
            );
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_cashier_screen_contains_a_pdf_invoice_download_action(): void
    {
        $cashier = User::factory()->create();
        $cashier->forceFill(['role' => 'cashier'])->save();

        $this->actingAs($cashier)
            ->get(route('cashier.index'))
            ->assertOk()
            ->assertSee('Unduh invoice PDF')
            ->assertSee('id="receiptModal"', false)
            ->assertSee('id="newTransaction"', false)
            ->assertSee(route('cashier.invoice', ['transaction' => '__TRANSACTION_ID__']));
    }

    public function test_cashier_cannot_download_another_cashiers_invoice(): void
    {
        $owner = User::factory()->create();
        $owner->forceFill(['role' => 'cashier'])->save();
        $anotherCashier = User::factory()->create();
        $anotherCashier->forceFill(['role' => 'cashier'])->save();
        $transaction = Transaction::create([
            'transaction_number' => 'TRX-20261025-002',
            'user_id' => $owner->id,
            'subtotal' => 10000,
            'discount' => 0,
            'tax' => 0,
            'other_fee' => 0,
            'grand_total' => 10000,
            'paid_amount' => 10000,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'status' => 'paid',
        ]);

        $this->actingAs($anotherCashier)
            ->get(route('cashier.invoice', $transaction))
            ->assertForbidden();
    }

    public function test_admin_can_download_invoice_but_customer_cannot(): void
    {
        $cashier = User::factory()->create();
        $cashier->forceFill(['role' => 'cashier'])->save();
        $transaction = Transaction::create([
            'transaction_number' => 'TRX-20261025-003',
            'user_id' => $cashier->id,
            'subtotal' => 10000,
            'discount' => 0,
            'tax' => 0,
            'other_fee' => 0,
            'grand_total' => 10000,
            'paid_amount' => 10000,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'status' => 'paid',
        ]);

        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $this->actingAs($admin)
            ->get(route('cashier.invoice', $transaction))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get(route('cashier.invoice', $transaction))
            ->assertForbidden();
    }
}
