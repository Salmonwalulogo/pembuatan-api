<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_routes_do_not_replace_existing_product_route_names(): void
    {
        $this->assertSame(url('/products'), route('products.index'));
        $this->assertSame(url('/api/products'), route('api.products.index'));
    }

    public function test_admin_can_login_and_manage_products_with_a_bearer_token(): void
    {
        $admin = User::factory()->create(['password' => 'password']);
        $admin->forceFill(['role' => 'admin'])->save();

        $login = $this->postJson('/api/login', [
            'email' => $admin->email,
            'password' => 'password',
            'device_name' => 'Postman',
        ])->assertOk()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.email', $admin->email);

        $token = $login->json('token');
        $this->assertNotEmpty($token);

        $created = $this->withToken($token)->withHeader('Accept', 'application/json')->post('/api/products', [
            'name' => 'Kopi',
            'product_code' => 'PRD-001',
            'barcode' => '899000000001',
            'category' => 'Minuman',
            'description' => 'Kopi bubuk',
            'price' => 15000,
            'stock' => 20,
            'is_active' => true,
        ])->assertCreated()
            ->assertJsonPath('name', 'Kopi');

        $product = Product::findOrFail($created->json('id'));

        $this->withToken($token)->getJson('/api/products')
            ->assertOk()
            ->assertJsonPath('data.0.id', $product->id);

        $this->withToken($token)->getJson("/api/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('name', 'Kopi');

        $this->withToken($token)->withHeader('Accept', 'application/json')->patch("/api/products/{$product->id}", [
            'price' => 17500,
        ])->assertOk()
            ->assertJsonPath('price', '17500.00');

        $this->withToken($token)->deleteJson("/api/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Produk berhasil dihapus.');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_product_api_rejects_missing_invalid_and_non_admin_credentials(): void
    {
        $this->getJson('/api/products')->assertUnauthorized();

        $this->postJson('/api/login', [
            'email' => 'admin@example.test',
            'password' => 'wrong-password',
        ])->assertUnauthorized();

        $customer = User::factory()->create(['password' => 'password']);

        $this->postJson('/api/login', [
            'email' => $customer->email,
            'password' => 'password',
        ])->assertForbidden();
    }

    public function test_product_api_validates_product_fields_and_revokes_tokens_on_logout(): void
    {
        $admin = User::factory()->create(['password' => 'password']);
        $admin->forceFill(['role' => 'admin'])->save();
        $token = $this->postJson('/api/login', [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertOk()->json('token');

        $this->withToken($token)->postJson('/api/products', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'category', 'price', 'stock']);

        $this->withToken($token)->postJson('/api/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Token berhasil dicabut.');

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $admin->id,
        ]);

        Auth::forgetGuards();

        $this->withToken($token)->getJson('/api/products')
            ->assertUnauthorized();
    }
}
