<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Tests\TestCase;

class ProductImageProcessingTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_image_is_rotated_cropped_resized_watermarked_and_saved_as_webp(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $this->actingAs($admin)
            ->post(route('products.store'), [
                'name' => 'Produk Gambar',
                'category' => 'Demo',
                'price' => 2500,
                'stock' => 5,
                'image_rotation' => 90,
                'image' => $this->splitColorImage(),
            ])
            ->assertRedirect(route('products.index'));

        $product = Product::query()->firstOrFail();
        $this->assertStringEndsWith('.webp', $product->image);
        Storage::disk('public')->assertExists($product->image);

        $image = (new ImageManager(new Driver))
            ->read(Storage::disk('public')->path($product->image));

        $this->assertSame(1200, $image->width());
        $this->assertSame(1200, $image->height());

        $processedImage = imagecreatefromwebp(Storage::disk('public')->path($product->image));
        $topPixel = imagecolorat($processedImage, 600, 300);
        $bottomPixel = imagecolorat($processedImage, 600, 900);
        $this->assertGreaterThan(100, abs((($topPixel >> 16) & 0xFF) - (($bottomPixel >> 16) & 0xFF)));

        $watermarkIsVisible = false;

        for ($x = 700; $x < 1200 && ! $watermarkIsVisible; $x++) {
            for ($y = 1100; $y < 1200; $y++) {
                $color = imagecolorat($processedImage, $x, $y);
                if (($color >> 16 & 0xFF) > 200) {
                    $watermarkIsVisible = true;
                    break;
                }
            }
        }

        imagedestroy($processedImage);
        $this->assertTrue($watermarkIsVisible);
    }

    public function test_product_api_uses_the_same_image_processing_pipeline(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $token = $admin->createToken('image-upload', ['products:manage'])->plainTextToken;

        $response = $this->withToken($token)
            ->withHeader('Accept', 'application/json')
            ->post(route('api.products.store'), [
                'name' => 'Produk API',
                'category' => 'Demo',
                'price' => 5000,
                'stock' => 3,
                'image_rotation' => 180,
                'image' => UploadedFile::fake()->image('product.png', 900, 1600),
            ]);

        $response->assertCreated();
        $product = Product::query()->findOrFail($response->json('id'));

        $this->assertStringEndsWith('.webp', $product->image);
        Storage::disk('public')->assertExists($product->image);
        $response->assertJsonPath('image', $product->image)
            ->assertJsonPath('image_url', asset('storage/'.$product->image));
    }

    private function splitColorImage(): UploadedFile
    {
        $image = imagecreatetruecolor(400, 200);
        imagefilledrectangle($image, 0, 0, 199, 199, imagecolorallocate($image, 255, 0, 0));
        imagefilledrectangle($image, 200, 0, 399, 199, imagecolorallocate($image, 0, 0, 255));
        ob_start();
        imagepng($image);
        $contents = ob_get_clean();
        imagedestroy($image);

        return UploadedFile::fake()->createWithContent('product.png', $contents);
    }
}
