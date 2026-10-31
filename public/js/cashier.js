(function () {
    'use strict';

    const products = Array.isArray(window.cashierProducts) ? window.cashierProducts : [];
    const state = { cart: [], method: 'cash', transactionNumber: window.cashierTransactionNumber, held: JSON.parse(localStorage.getItem('cashier-held') || '[]') };
    const $ = (id) => document.getElementById(id);
    const rupiah = (value) => 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(Math.max(0, Number(value) || 0));
    const numberValue = (id) => Math.max(0, Number($(id).value) || 0);
    const calculations = () => {
        const subtotal = state.cart.reduce((sum, item) => sum + item.price * item.qty, 0);
        const percent = Math.min(100, numberValue('discountPercent'));
        const discount = Math.floor(subtotal * percent / 100) + numberValue('discountNominal');
        const total = Math.max(0, subtotal - discount + numberValue('tax') + numberValue('otherFee'));
        const paid = numberValue('paidAmount');
        return { subtotal, discount, total, paid, change: paid - total, items: state.cart.reduce((sum, item) => sum + item.qty, 0) };
    };
    const showAlert = (message, type = 'danger') => { const box = $('alertBox'); box.className = 'alert alert-' + type; box.textContent = message; window.scrollTo({ top: 0, behavior: 'smooth' }); setTimeout(() => box.classList.add('d-none'), 4500); };
    const renderProducts = (query = '') => {
        const normalized = query.trim().toLowerCase();
        const matches = products.filter((product) => !normalized || [product.name, product.product_code, product.barcode].some((field) => String(field || '').toLowerCase().includes(normalized))).slice(0, 9);
        $('productResults').innerHTML = matches.map((product) => `<article class="product-result${Number(product.stock) < 1 ? ' out-of-stock' : ''}" data-product="${product.id}"><span class="code">${product.product_code || '-'}</span><h3>${escapeHtml(product.name)}</h3><p>${escapeHtml(product.barcode || 'Barcode belum diisi')}</p><small class="stock-label">${Number(product.stock) > 0 ? `Stok ${product.stock}` : 'Stok habis'}</small><strong>${rupiah(product.price)}</strong></article>`).join('') || '<p class="muted">Barang tidak ditemukan.</p>';
        document.querySelectorAll('.product-result').forEach((card) => card.addEventListener('click', () => addProduct(Number(card.dataset.product))));
    };
    const renderCart = () => {
        $('cartBadge').textContent = calculations().items;
        if (!state.cart.length) { $('cartContent').innerHTML = '<div class="empty-state"><i class="bi bi-cart-x"></i><h3>Keranjang masih kosong</h3><p>Tambahkan barang dari pencarian di atas.</p></div>'; return; }
        $('cartContent').innerHTML = `<table class="cart-table"><thead><tr><th>Barang</th><th>Qty</th><th>Harga</th><th>Jumlah</th><th></th></tr></thead><tbody>${state.cart.map((item, index) => `<tr><td><span class="item-name">${escapeHtml(item.name)}</span><span class="item-code">${escapeHtml(item.product_code || '')}</span></td><td><div class="qty-control"><button type="button" data-action="minus" data-index="${index}">−</button><input type="number" min="1" value="${item.qty}" data-action="input" data-index="${index}"><button type="button" data-action="plus" data-index="${index}">+</button></div></td><td>${rupiah(item.price)}</td><td><strong>${rupiah(item.price * item.qty)}</strong></td><td><button type="button" class="remove-item" data-action="remove" data-index="${index}" title="Hapus"><i class="bi bi-trash"></i></button></td></tr>`).join('')}</tbody></table>`;
        document.querySelectorAll('[data-action]').forEach((element) => element.addEventListener('click', handleCartAction));
        document.querySelectorAll('[data-action="input"]').forEach((element) => element.addEventListener('change', handleCartAction));
    };
    const handleCartAction = (event) => {
        const index = Number(event.currentTarget.dataset.index);
        const action = event.currentTarget.dataset.action;
        if (action === 'plus') state.cart[index].qty += 1;
        if (action === 'minus') state.cart[index].qty = Math.max(1, state.cart[index].qty - 1);
        if (action === 'input') state.cart[index].qty = Math.max(1, parseInt(event.currentTarget.value, 10) || 1);
        if (action === 'remove') state.cart.splice(index, 1);
        renderCart(); calculateAndRender();
    };
    const addProduct = (id) => {
        const product = products.find((item) => item.id === id);
        if (!product || Number(product.price) < 0) return;
        const existing = state.cart.find((item) => item.id === id);
        if (Number(product.stock) < 1) return showAlert(`Stok ${product.name} sedang habis.`);
        if (existing && existing.qty >= Number(product.stock)) return showAlert(`Stok ${product.name} hanya tersedia ${product.stock}.`);
        if (existing) existing.qty += 1;
        else state.cart.push({ id: product.id, product_code: product.product_code, name: product.name, price: Number(product.price), qty: 1 });
        renderCart(); calculateAndRender(); $('productSearch').focus(); showAlert(product.name + ' ditambahkan ke keranjang.', 'success');
    };
    const calculateAndRender = () => {
        const result = calculations();
        $('totalItems').textContent = result.items;
        $('subtotal').textContent = rupiah(result.subtotal);
        $('grandTotal').textContent = rupiah(result.total);
        const box = $('changeBox');
        box.className = 'change-box ' + (result.paid === 0 && result.total === 0 ? 'neutral' : result.change < 0 ? 'danger' : 'success');
        $('changeLabel').textContent = result.change < 0 ? 'UANG KURANG' : 'KEMBALIAN';
        $('changeAmount').textContent = rupiah(Math.abs(result.change));
        $('payButton').disabled = !state.cart.length || result.change < 0;
    };
    const holdTransaction = () => {
        if (!state.cart.length) return showAlert('Keranjang kosong, tidak ada transaksi yang dapat ditahan.');
        const result = calculations();
        state.held.push({ id: Date.now(), number: state.transactionNumber, cart: state.cart, customer: $('customerName').value, total: result.total, discountPercent: $('discountPercent').value, discountNominal: $('discountNominal').value, tax: $('tax').value, otherFee: $('otherFee').value, paid: $('paidAmount').value });
        localStorage.setItem('cashier-held', JSON.stringify(state.held)); resetTransaction(); renderHeld(); showAlert('Transaksi berhasil ditahan.', 'success');
    };
    const renderHeld = () => { $('heldCount').textContent = state.held.length; $('heldTransactions').innerHTML = state.held.length ? state.held.map((item) => `<div class="held-item"><div><strong>${item.number}</strong><small>${item.cart.reduce((sum, product) => sum + product.qty, 0)} Item · ${rupiah(item.total)}</small></div><button class="btn btn-sm btn-outline-primary" data-resume="${item.id}">Lanjutkan</button></div>`).join('') : '<p class="muted">Belum ada transaksi yang ditahan.</p>'; document.querySelectorAll('[data-resume]').forEach((button) => button.addEventListener('click', () => resumeTransaction(Number(button.dataset.resume)))); };
    const resumeTransaction = (id) => { const item = state.held.find((held) => held.id === id); if (!item) return; state.cart = item.cart; state.transactionNumber = item.number; $('transactionNumber').textContent = item.number; $('customerName').value = item.customer; ['discountPercent', 'discountNominal', 'tax', 'otherFee', 'paidAmount'].forEach((field) => $(field).value = item[field === 'paidAmount' ? 'paid' : field]); state.held = state.held.filter((held) => held.id !== id); localStorage.setItem('cashier-held', JSON.stringify(state.held)); renderCart(); renderHeld(); calculateAndRender(); };
    const resetTransaction = () => { state.cart = []; state.transactionNumber = nextTransactionNumber(); $('transactionNumber').textContent = state.transactionNumber; ['customerName', 'paidAmount'].forEach((field) => $(field).value = ''); ['discountPercent', 'discountNominal', 'tax', 'otherFee'].forEach((field) => $(field).value = '0'); renderCart(); calculateAndRender(); };
    const nextTransactionNumber = () => { const parts = String(state.transactionNumber).split('-'); parts[2] = String((Number(parts[2]) || 0) + 1).padStart(3, '0'); return parts.join('-'); };
    const submitPayment = async () => {
        const result = calculations();
        if (!state.cart.length) return showAlert('Keranjang tidak boleh kosong saat pembayaran.');
        if (result.change < 0) return showAlert('Pembayaran belum mencukupi total transaksi.');
        const button = $('payButton'); button.disabled = true;
        try {
            const payload = {
                items: state.cart.map((item) => ({ product_id: item.id, product_code: item.product_code, product_name: item.name, price: item.price, qty: item.qty })),
                customer_name: $('customerName').value || null,
                discount_percent: numberValue('discountPercent'),
                discount_nominal: numberValue('discountNominal'),
                tax: numberValue('tax'),
                other_fee: numberValue('otherFee'),
                paid_amount: result.paid,
                payment_method: state.method,
            };

            const response = await fetch('/kasir/transaksi', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify(payload) });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || Object.values(data.errors || {}).flat()[0] || 'Transaksi gagal disimpan.');

            const responseStruk = await fetch('/kasir/struk', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({
                    transaction_id: data.transaction.id,
                    transaction_number: data.transaction.transaction_number,
                    items: payload.items.map((item) => ({ name: item.product_name, qty: item.qty, price: item.price })),
                    customer_name: $('customerName').value || 'Pelanggan umum',
                    subtotal: data.transaction.subtotal,
                    discount: data.transaction.discount,
                    tax: data.transaction.tax,
                    other_fee: data.transaction.other_fee,
                    grand_total: data.transaction.grand_total,
                    paid_amount: data.transaction.paid_amount,
                    change_amount: data.transaction.change_amount,
                    payment_method: data.transaction.payment_method,
                }),
            });

            const strukData = await responseStruk.json().catch(() => ({}));
            if (!responseStruk.ok && !strukData.success) {
                showAlert(strukData.message || 'Struk gagal dicetak, transaksi tetap tersimpan.');
            }

            fillReceipt(data.transaction, result);
            resetTransaction();
            openReceiptModal();
        } catch (error) { showAlert(error.message); button.disabled = false; }
    };
    const fillReceipt = (transaction, result) => { $('receiptNumber').textContent = transaction.transaction_number; $('receiptDate').textContent = new Date().toLocaleString('id-ID'); $('receiptCustomer').textContent = $('customerName').value || 'Pelanggan umum'; $('receiptItems').innerHTML = state.cart.map((item) => `<tr><td>${escapeHtml(item.name)}</td><td>${item.qty}</td><td>${rupiah(item.price)}</td><td>${rupiah(item.price * item.qty)}</td></tr>`).join(''); $('receiptPrintItems').innerHTML = state.cart.map((item) => `<div class="receipt-print-item"><span>${escapeHtml(item.name)}</span><span>${item.qty} x ${rupiah(item.price)} = ${rupiah(item.price * item.qty)}</span></div>`).join(''); $('receiptSubtotal').textContent = rupiah(transaction.subtotal || result.subtotal); $('receiptDiscount').textContent = rupiah(transaction.discount || result.discount); $('receiptTax').textContent = rupiah(transaction.tax || numberValue('tax')); $('receiptFee').textContent = rupiah(transaction.other_fee || numberValue('otherFee')); $('receiptTotal').textContent = rupiah(transaction.grand_total || result.total); $('receiptPaid').textContent = rupiah(transaction.paid_amount || result.paid); $('receiptChange').textContent = rupiah(transaction.change_amount || result.change); $('receiptMethod').textContent = (transaction.payment_method || state.method || 'Tunai').toString(); const invoice = $('downloadInvoice'); invoice.href = invoice.dataset.url.replace('__TRANSACTION_ID__', encodeURIComponent(transaction.id)); invoice.hidden = false; };
    const openReceiptModal = () => {
        $('receiptModal').hidden = false;
        $('receiptModal').setAttribute('aria-hidden', 'false');
        document.body.classList.add('receipt-modal-open');
        $('newTransaction').focus();
    };
    const closeReceiptModal = () => {
        $('receiptModal').hidden = true;
        $('receiptModal').setAttribute('aria-hidden', 'true');
        document.body.classList.remove('receipt-modal-open');
        $('productSearch').focus();
    };
    const escapeHtml = (value) => String(value || '').replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[char]));
    $('productSearch').addEventListener('input', (event) => { $('clearSearch').classList.toggle('d-none', !event.target.value); renderProducts(event.target.value); });
    $('clearSearch').addEventListener('click', () => { $('productSearch').value = ''; $('clearSearch').classList.add('d-none'); renderProducts(); $('productSearch').focus(); });
    ['discountPercent', 'discountNominal', 'tax', 'otherFee', 'paidAmount'].forEach((id) => $(id).addEventListener('input', calculateAndRender));
    document.querySelectorAll('[data-quick]').forEach((button) => button.addEventListener('click', () => { $('paidAmount').value = button.dataset.quick; calculateAndRender(); }));
    $('exactPayment').addEventListener('click', () => { $('paidAmount').value = calculations().total; calculateAndRender(); });
    document.querySelectorAll('.method').forEach((button) => button.addEventListener('click', () => { document.querySelectorAll('.method').forEach((item) => item.classList.remove('active')); button.classList.add('active'); state.method = button.dataset.method; }));
    $('clearCart').addEventListener('click', () => { if (state.cart.length && confirm('Kosongkan semua barang di keranjang?')) resetTransaction(); });
    $('cancelTransaction').addEventListener('click', () => { if (confirm('Batalkan transaksi ini?')) resetTransaction(); });
    $('holdTransaction').addEventListener('click', holdTransaction); $('payButton').addEventListener('click', submitPayment);
    document.querySelectorAll('[data-close-receipt]').forEach((button) => button.addEventListener('click', closeReceiptModal));
    $('newTransaction').addEventListener('click', closeReceiptModal);
    $('printReceipt').addEventListener('click', () => window.print());
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !$('receiptModal').hidden) {
            closeReceiptModal();
            return;
        }
        if (event.key === 'F2') { event.preventDefault(); $('productSearch').focus(); }
        if (event.key === 'F4') { event.preventDefault(); $('paidAmount').focus(); }
        if (event.key === 'Escape') $('cancelTransaction').click();
    });
    setInterval(() => { $('currentDateTime').textContent = new Date().toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }); }, 1000);
    renderProducts(); renderCart(); renderHeld(); calculateAndRender();
}());
