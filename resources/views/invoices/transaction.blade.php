<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $transaction->transaction_number }}</title>
    <style>
        body { color: #1f2937; font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { color: #111827; font-size: 24px; margin-bottom: 4px; }
        .muted { color: #6b7280; }
        .header, .summary { width: 100%; }
        .header td { vertical-align: top; }
        .right { text-align: right; }
        table.items { border-collapse: collapse; margin-top: 28px; width: 100%; }
        .items th { background: #f3f4f6; text-align: left; }
        .items th, .items td { border-bottom: 1px solid #e5e7eb; padding: 9px 7px; }
        .items .number { text-align: right; }
        .summary { margin-top: 22px; }
        .summary td { padding: 5px 0; }
        .summary .total { border-top: 1px solid #9ca3af; font-size: 15px; font-weight: bold; }
        .thanks { border-top: 1px solid #e5e7eb; margin-top: 36px; padding-top: 14px; text-align: center; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                <h1>{{ config('app.name', 'SABU JAYA MART') }}</h1>
                <div class="muted">Invoice transaksi</div>
            </td>
            <td class="right">
                <strong>{{ $transaction->transaction_number }}</strong><br>
                {{ $transaction->created_at->format('d/m/Y H:i') }}
            </td>
        </tr>
    </table>

    <p>
        Pelanggan: {{ $transaction->customer_name ?: 'Pelanggan umum' }}<br>
        Kasir: {{ $transaction->user?->name ?: 'Kasir' }}<br>
        Metode pembayaran: {{ strtoupper($transaction->payment_method) }}
    </p>

    <table class="items">
        <thead>
            <tr>
                <th>Produk</th>
                <th class="number">Qty</th>
                <th class="number">Harga</th>
                <th class="number">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->details as $detail)
                <tr>
                    <td>{{ $detail->product_name }}</td>
                    <td class="number">{{ number_format($detail->qty, 0, ',', '.') }}</td>
                    <td class="number">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                    <td class="number">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary">
        <tr><td>Subtotal</td><td class="right">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td></tr>
        <tr><td>Diskon</td><td class="right">Rp {{ number_format($transaction->discount, 0, ',', '.') }}</td></tr>
        <tr><td>Pajak</td><td class="right">Rp {{ number_format($transaction->tax, 0, ',', '.') }}</td></tr>
        <tr><td>Biaya tambahan</td><td class="right">Rp {{ number_format($transaction->other_fee, 0, ',', '.') }}</td></tr>
        <tr class="total"><td>Total</td><td class="right">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</td></tr>
        <tr><td>Dibayar</td><td class="right">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</td></tr>
        <tr><td>Kembalian</td><td class="right">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</td></tr>
    </table>

    <div class="thanks">Terima kasih telah berbelanja.</div>
</body>
</html>
