@php($isEdit = isset($product) && $product->exists)

<form action="{{ $isEdit ? route('products.update', $product) : route('products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label for="name" class="form-label">Nama Produk <span class="text-danger">*</span></label>
            <input id="name" name="name" type="text" required class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product?->name) }}">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
            <input id="category" name="category" type="text" required class="form-control @error('category') is-invalid @enderror" value="{{ old('category', $product?->category) }}">
            @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label for="product_code" class="form-label">Kode Produk</label>
            <input id="product_code" name="product_code" type="text" class="form-control @error('product_code') is-invalid @enderror" value="{{ old('product_code', $product?->product_code) }}">
            @error('product_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label for="barcode" class="form-label">Barcode</label>
            <input id="barcode" name="barcode" type="text" class="form-control @error('barcode') is-invalid @enderror" value="{{ old('barcode', $product?->barcode) }}">
            @error('barcode')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            <label for="description" class="form-label">Deskripsi</label>
            <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $product?->description) }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label for="price" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
            <input id="price" name="price" type="number" min="0" step="0.01" required class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $product?->price) }}">
            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label for="stock" class="form-label">Stok <span class="text-danger">*</span></label>
            <input id="stock" name="stock" type="number" min="0" required class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', $product?->stock ?? 0) }}">
            @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label for="image" class="form-label">Gambar Produk</label>
            <input id="image" name="image" type="file" accept="image/*" class="form-control @error('image') is-invalid @enderror">
            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="form-text">Gambar dipotong menjadi 1200 × 1200, diberi watermark, dan disimpan sebagai WebP.</small>
            @if($isEdit && $product->image)
                <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="img-thumbnail mt-2" style="max-width:150px">
            @endif
        </div>
        <div class="col-md-6">
            <label for="image_rotation" class="form-label">Rotasi Gambar</label>
            <select id="image_rotation" name="image_rotation" class="form-select @error('image_rotation') is-invalid @enderror">
                @foreach([0 => 'Tidak diputar', 90 => '90° searah jarum jam', 180 => '180°', 270 => '90° berlawanan arah jarum jam'] as $degrees => $label)
                    <option value="{{ $degrees }}" @selected((int) old('image_rotation', 0) === $degrees)>{{ $label }}</option>
                @endforeach
            </select>
            @error('image_rotation')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 d-flex align-items-end">
            <div class="form-check form-switch">
                <input id="is_active" name="is_active" type="checkbox" value="1" class="form-check-input" @checked(old('is_active', $product?->is_active ?? true))>
                <label for="is_active" class="form-check-label">Produk Aktif</label>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('products.index') }}" class="btn btn-light border">Batal</a>
        <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Perbarui' : 'Simpan' }}</button>
    </div>
</form>
