<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kasir | {{ config('app.name', 'SABU JAYA MART') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/cashier.css') }}" rel="stylesheet">
</head>
<body>
<main class="cashier-shell">
    <header class="store-header">
        <div>
            <span class="eyebrow">POINT OF SALE</span>
            <h1><i class="bi bi-shop-window"></i> {{ config('app.name', 'SABU JAYA MART') }}</h1>
            <p>Kelola transaksi dengan cepat dan akurat</p>
        </div>
        <div class="header-meta">
            <div><span>Nomor Transaksi</span><strong id="transactionNumber">{{ $transactionNumber }}</strong></div>
            <div><span>Tanggal & Waktu</span><strong id="currentDateTime">-</strong></div>
            <div><span>Kasir</span><strong>{{ auth()->user()?->name ?? 'Kasir Utama' }}</strong></div>
            <form method="POST" action="{{ route('logout') }}" class="cashier-logout-form">
                @csrf
                <button type="submit" class="cashier-logout">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </header>

    <div id="alertBox" class="alert d-none" role="alert"></div>
    <section class="pos-grid">
        <div class="main-panel">
            <div class="panel-card search-card">
                <div class="section-heading">
                    <div><span class="section-icon"><i class="bi bi-bag-plus"></i></span><div><h2>Tambah Barang</h2><p>Cari berdasarkan nama, kode, atau barcode</p></div></div>
                    <span class="shortcut-hint">F2</span>
                </div>
                <div class="search-wrap">
                    <i class="bi bi-search"></i>
                    <input id="productSearch" class="form-control" type="search" placeholder="Ketik nama barang, kode, atau scan barcode..." autocomplete="off">
                    <button id="clearSearch" class="btn btn-link d-none" type="button"><i class="bi bi-x-circle"></i></button>
                </div>
                <div id="productResults" class="product-results"></div>
            </div>

            <div class="panel-card customer-card">
                <label for="customerName"><i class="bi bi-person"></i> Pelanggan <small>(opsional)</small></label>
                <input id="customerName" class="form-control" type="text" placeholder="Pelanggan umum">
            </div>

            <div class="panel-card cart-card">
                <div class="section-heading">
                    <div><span class="section-icon"><i class="bi bi-cart3"></i></span><div><h2>Keranjang Belanja <span id="cartBadge" class="badge">0</span></h2><p>Periksa barang sebelum melanjutkan pembayaran</p></div></div>
                    <button id="clearCart" class="btn btn-sm btn-outline-danger" type="button"><i class="bi bi-trash3"></i> Kosongkan</button>
                </div>
                <div id="cartContent" class="cart-content">
                    <div class="empty-state"><i class="bi bi-cart-x"></i><h3>Keranjang masih kosong</h3><p>Tambahkan barang dari pencarian di atas.</p></div>
                </div>
            </div>
        </div>

        <aside class="summary-panel">
            <div class="panel-card summary-card">
                <div class="section-heading"><div><span class="section-icon"><i class="bi bi-receipt"></i></span><div><h2>Ringkasan Pembayaran</h2><p>Hitung total dan selesaikan transaksi</p></div></div></div>
                <div class="summary-rows">
                    <div><span>Total Item</span><strong id="totalItems">0</strong></div>
                    <div><span>Subtotal</span><strong id="subtotal">Rp 0</strong></div>
                    <div class="input-row"><label for="discountPercent">Diskon (%)</label><div class="input-group input-group-sm"><input id="discountPercent" class="form-control" type="number" min="0" max="100" value="0"><span class="input-group-text">%</span></div></div>
                    <div><span>Diskon Nominal</span><div class="money-input"><span>Rp</span><input id="discountNominal" type="number" min="0" value="0"></div></div>
                    <div><span>Pajak / PPN</span><div class="money-input"><span>Rp</span><input id="tax" type="number" min="0" value="0"></div></div>
                    <div><span>Biaya Tambahan</span><div class="money-input"><span>Rp</span><input id="otherFee" type="number" min="0" value="0"></div></div>
                </div>
                <div class="grand-total"><span>Total Akhir</span><strong id="grandTotal">Rp 0</strong></div>
                <div class="payment-box">
                    <div class="payment-label"><label for="paidAmount">Uang Dibayar</label><span class="shortcut-hint">F4</span></div>
                    <div class="paid-input"><span>Rp</span><input id="paidAmount" type="number" min="0" placeholder="0"></div>
                    <div class="quick-payments"><button type="button" data-quick="40000">Rp 40.000</button><button type="button" data-quick="50000">Rp 50.000</button><button type="button" data-quick="100000">Rp 100.000</button><button type="button" id="exactPayment">Uang Pas</button></div>
                </div>
                <div class="payment-method">
                    <label>Metode Pembayaran</label>
                    <div class="method-grid">
                        <button type="button" class="method active" data-method="cash"><i class="bi bi-cash-coin"></i>Tunai</button>
                        <button type="button" class="method" data-method="qris"><i class="bi bi-qr-code-scan"></i>QRIS</button>
                        <button type="button" class="method" data-method="debit"><i class="bi bi-credit-card"></i>Debit</button>
                        <button type="button" class="method" data-method="credit"><i class="bi bi-credit-card-2-front"></i>Kredit</button>
                        <button type="button" class="method" data-method="e-wallet"><i class="bi bi-wallet2"></i>E-Wallet</button>
                        <button type="button" class="method" data-method="transfer"><i class="bi bi-bank"></i>Transfer</button>
                    </div>
                </div>
                <div id="changeBox" class="change-box neutral"><span id="changeLabel">KEMBALIAN</span><strong id="changeAmount">Rp 0</strong></div>
                <div class="action-buttons"><button id="holdTransaction" class="btn btn-outline-warning"><i class="bi bi-pause-circle"></i> Tahan</button><button id="cancelTransaction" class="btn btn-outline-danger"><i class="bi bi-x-circle"></i> Batal</button></div>
                <button id="payButton" class="btn btn-pay" type="button"><i class="bi bi-check2-circle"></i> BAYAR & CETAK</button>
            </div>
            <div class="panel-card held-card"><div class="held-heading"><h2><i class="bi bi-clock-history"></i> Transaksi Ditahan</h2><span id="heldCount" class="badge">0</span></div><div id="heldTransactions"><p class="muted">Belum ada transaksi yang ditahan.</p></div></div>
        </aside>
    </section>
</main>

<div id="receiptModal" class="receipt-modal" hidden>
    <button class="receipt-modal-backdrop" type="button" aria-label="Tutup popup struk" data-close-receipt></button>
    <section class="receipt-dialog" role="dialog" aria-modal="true" aria-labelledby="receiptModalTitle">
        <header class="receipt-modal-header">
            <button class="receipt-modal-close" type="button" aria-label="Tutup popup struk" data-close-receipt>
                <i class="bi bi-x-lg"></i>
            </button>
            <span class="receipt-success-icon"><i class="bi bi-check-lg"></i></span>
            <span class="receipt-modal-eyebrow">PEMBAYARAN BERHASIL</span>
            <h2 id="receiptModalTitle">Transaksi selesai</h2>
            <p>Terima kasih, pembayaran Anda sudah tercatat.</p>
        </header>

        <section id="receipt" class="receipt" aria-label="Rincian struk pembayaran">
            <div class="receipt-header"><h1>{{ strtoupper(config('app.name', 'SABU JAYA MART')) }}</h1><p>Struk Pembayaran</p></div>
            <div class="receipt-info"><div>No. Transaksi <strong id="receiptNumber"></strong></div><div>Tanggal <strong id="receiptDate"></strong></div><div>Kasir <strong id="receiptCashier">{{ auth()->user()?->name ?? 'Kasir Utama' }}</strong></div><div>Pelanggan <strong id="receiptCustomer">Pelanggan umum</strong></div></div>
            <table class="receipt-screen-items"><thead><tr><th>Nama Barang</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead><tbody id="receiptItems"></tbody></table>
            <div id="receiptPrintItems" class="receipt-print-items"></div>
            <div class="receipt-totals"><div>Subtotal <strong id="receiptSubtotal"></strong></div><div>Diskon <strong id="receiptDiscount"></strong></div><div>Pajak <strong id="receiptTax"></strong></div><div>Biaya lain <strong id="receiptFee"></strong></div><div class="receipt-grand">TOTAL <strong id="receiptTotal"></strong></div><div>Bayar <strong id="receiptPaid"></strong></div><div>Kembali <strong id="receiptChange"></strong></div><div>Metode pembayaran <strong id="receiptMethod"></strong></div></div>
            <p class="receipt-thanks">Terima kasih telah berbelanja.<br>Simpan struk ini sebagai bukti pembayaran.</p>
        </section>

        <footer class="receipt-actions no-print">
            <button id="printReceipt" class="receipt-print-button" type="button"><i class="bi bi-printer"></i> Cetak struk</button>
            <a id="downloadInvoice" class="receipt-download-button" href="#" data-url="{{ route('cashier.invoice', ['transaction' => '__TRANSACTION_ID__']) }}" hidden>
                <i class="bi bi-file-earmark-pdf"></i> Unduh invoice PDF
            </a>
            <button id="newTransaction" class="receipt-new-button" type="button"><i class="bi bi-plus-lg"></i> Transaksi baru</button>
        </footer>
    </section>
</div>
<script>window.cashierProducts = @json($products); window.cashierTransactionNumber = @json($transactionNumber);</script>
<script src="{{ asset('js/cashier.js') }}"></script>
</body>
</html>
