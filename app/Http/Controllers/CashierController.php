<?php

namespace App\Http\Controllers;

use App\Events\OrderPlaced;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CashierController extends Controller
{
    private const DEMO_PRODUCTS = [
        ['product_code' => 'BRG001', 'barcode' => '899123456001', 'name' => 'Indomie Goreng', 'price' => 3500],
        ['product_code' => 'BRG002', 'barcode' => '899123456002', 'name' => 'Aqua 600ml', 'price' => 4000],
        ['product_code' => 'BRG003', 'barcode' => '899123456003', 'name' => 'Teh Botol Sosro', 'price' => 5000],
        ['product_code' => 'BRG004', 'barcode' => '899123456004', 'name' => 'Beras 5 Kg', 'price' => 75000],
        ['product_code' => 'BRG005', 'barcode' => '899123456005', 'name' => 'Minyak Goreng 1 Liter', 'price' => 18000],
        ['product_code' => 'BRG006', 'barcode' => '899123456006', 'name' => 'Gula Pasir 1 Kg', 'price' => 17000],
    ];

    public function index()
    {
        $this->seedDemoProductsIfEmpty();
        $products = Product::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'product_code', 'barcode', 'name', 'price', 'stock']);

        return view('cashier.index', [
            'products' => $products,
            'transactionNumber' => $this->nextTransactionNumber(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'discount_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'discount_nominal' => ['required', 'integer', 'min:0'],
            'tax' => ['required', 'integer', 'min:0'],
            'other_fee' => ['required', 'integer', 'min:0'],
            'paid_amount' => ['required', 'integer', 'min:0'],
            'payment_method' => ['required', 'in:cash,qris,debit,credit,e-wallet,transfer'],
            'customer_name' => ['nullable', 'string', 'max:255'],
        ]);

        $requestedItems = collect($validated['items'])
            ->groupBy('product_id')
            ->map(fn ($group) => [
                'product_id' => (int) $group->first()['product_id'],
                'qty' => $group->sum('qty'),
            ])
            ->values();

        $products = Product::query()
            ->whereIn('id', $requestedItems->pluck('product_id'))
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        if ($products->count() !== $requestedItems->count()) {
            throw ValidationException::withMessages([
                'items' => 'Salah satu produk tidak tersedia atau sudah tidak aktif.',
            ]);
        }

        $items = $requestedItems->map(function (array $item) use ($products) {
            $product = $products->get($item['product_id']);

            return [
                'product' => $product,
                'qty' => $item['qty'],
                'price' => (int) $product->price,
            ];
        });

        $subtotal = $items->sum(fn (array $item) => $item['price'] * $item['qty']);
        $discountPercent = (int) floor($subtotal * ((float) $validated['discount_percent']) / 100);
        $discount = $discountPercent + $validated['discount_nominal'];
        $grandTotal = max(0, $subtotal - $discount + $validated['tax'] + $validated['other_fee']);

        if ($validated['paid_amount'] < $grandTotal) {
            throw ValidationException::withMessages([
                'paid_amount' => 'Pembayaran kurang dari total transaksi.',
            ]);
        }

        $transaction = DB::transaction(function () use ($validated, $items, $subtotal, $discount, $grandTotal) {
            foreach ($items as $item) {
                $product = Product::query()->lockForUpdate()->find($item['product']->id);

                if (! $product || ! $product->is_active || $product->stock < $item['qty']) {
                    throw ValidationException::withMessages([
                        'items' => "Stok produk {$item['product']->name} tidak mencukupi.",
                    ]);
                }

                $product->decrement('stock', $item['qty']);
            }

            $transaction = Transaction::create([
                'transaction_number' => $this->nextTransactionNumber(),
                'user_id' => auth()->id(),
                'customer_name' => $validated['customer_name'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $validated['tax'],
                'other_fee' => $validated['other_fee'],
                'grand_total' => $grandTotal,
                'paid_amount' => $validated['paid_amount'],
                'change_amount' => $validated['paid_amount'] - $grandTotal,
                'payment_method' => $validated['payment_method'],
                'status' => 'paid',
            ]);

            $transaction->details()->createMany($items->map(fn (array $item) => [
                'product_id' => $item['product']->id,
                'product_code' => $item['product']->product_code,
                'product_name' => $item['product']->name,
                'price' => $item['price'],
                'qty' => $item['qty'],
                'subtotal' => $item['price'] * $item['qty'],
            ])->all());

            return $transaction->load('details');
        });

        OrderPlaced::dispatch(
            transactionNumber: $transaction->transaction_number,
            grandTotal: $transaction->grand_total,
            paymentMethod: $transaction->payment_method,
            cashierName: auth()->user()->name,
        );

        return response()->json([
            'message' => 'Transaksi berhasil disimpan.',
            'transaction' => $transaction,
        ], 201);
    }

    private function seedDemoProductsIfEmpty(): void
    {
        if (Product::query()->exists()) {
            return;
        }

        foreach (self::DEMO_PRODUCTS as $product) {
            Product::create(array_merge($product, [
                'category' => 'Demo',
                'stock' => 999,
                'is_active' => true,
            ]));
        }
    }

    private function nextTransactionNumber(): string
    {
        $prefix = 'TRX-'.now()->format('Ymd').'-';
        $last = Transaction::query()
            ->where('transaction_number', 'like', $prefix.'%')
            ->latest('id')
            ->value('transaction_number');
        $sequence = $last ? ((int) substr($last, -3)) + 1 : 1;

        return $prefix.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }
}
