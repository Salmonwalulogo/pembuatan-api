import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const notificationContainer = document.getElementById('transaction-notifications');

if (notificationContainer && window.Echo) {
    window.Echo.private('transactions').listen('.order.placed', (transaction) => {
        const notification = document.createElement('section');
        notification.className = 'transaction-notification';
        notification.setAttribute('role', 'status');

        const heading = document.createElement('strong');
        heading.textContent = 'Transaksi baru diterima';

        const details = document.createElement('p');
        const total = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        }).format(Number(transaction.grand_total) || 0);
        details.textContent = `${transaction.transaction_number} - ${transaction.cashier_name} - ${total}`;

        const dismiss = document.createElement('button');
        dismiss.type = 'button';
        dismiss.className = 'transaction-notification-dismiss';
        dismiss.setAttribute('aria-label', 'Tutup notifikasi');
        dismiss.textContent = '×';
        dismiss.addEventListener('click', () => notification.remove());

        notification.append(heading, details, dismiss);
        notificationContainer.append(notification);
        window.setTimeout(() => notification.remove(), 7000);
    });
}
