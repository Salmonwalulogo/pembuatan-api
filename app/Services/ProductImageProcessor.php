<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Typography\FontFactory;
use RuntimeException;

class ProductImageProcessor
{
    public function store(UploadedFile $file, int $rotation = 0): string
    {
        $image = (new ImageManager(new Driver))
            ->read($file->getRealPath())
            ->rotate($rotation)
            ->cover(1200, 1200);

        $watermark = config('app.name', 'SABU JAYA MART');
        $image->text($watermark, 1180, 1180, function (FontFactory $font): void {
            $font->filename(base_path('vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf'));
            $font->size(24);
            $font->color('#ffffff');
            $font->align('right');
            $font->valign('bottom');
            $font->stroke('#000000', 1);
        });

        $path = 'products/'.Str::uuid().'.webp';
        $stored = Storage::disk('public')->put(
            $path,
            (string) $image->toWebp(quality: 85)
        );

        if (! $stored) {
            throw new RuntimeException('Gambar produk gagal disimpan.');
        }

        return $path;
    }
}
