<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class StrukController extends Controller
{
    private int $paperWidth = 32;

    public function struk(Request $request)
    {
        $validated = $request->validate([
            'transaction_id' => ['nullable', 'integer', 'exists:transactions,id'],
            'transaction_number' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'subtotal' => ['nullable', 'integer', 'min:0'],
            'discount' => ['nullable', 'integer', 'min:0'],
            'tax' => ['nullable', 'integer', 'min:0'],
            'other_fee' => ['nullable', 'integer', 'min:0'],
            'grand_total' => ['nullable', 'integer', 'min:0'],
            'paid_amount' => ['nullable', 'integer', 'min:0'],
            'change_amount' => ['nullable', 'integer', 'min:0'],
            'payment_method' => ['nullable', 'string'],
        ]);

        $transaction = null;

        if (! empty($validated['transaction_id'])) {
            $transaction = Transaction::with('details')->findOrFail($validated['transaction_id']);
        } elseif (! empty($validated['transaction_number'])) {
            $transaction = Transaction::with('details')->where('transaction_number', $validated['transaction_number'])->first();
        }

        $items = $transaction ? $transaction->details->map(fn ($detail) => [
            'name' => $detail->product_name,
            'qty' => (int) $detail->qty,
            'price' => (int) $detail->price,
            'subtotal' => (int) $detail->subtotal,
        ])->values()->all() : ($validated['items'] ?? []);

        $customerName = $transaction?->customer_name ?? $validated['customer_name'] ?? 'Pelanggan umum';
        $transactionNumber = $transaction?->transaction_number ?? $validated['transaction_number'] ?? 'TRX-UNKNOWN';
        $subtotal = (int) ($transaction?->subtotal ?? $validated['subtotal'] ?? collect($items)->sum(fn ($item) => $item['qty'] * $item['price']));
        $discount = (int) ($transaction?->discount ?? $validated['discount'] ?? 0);
        $tax = (int) ($transaction?->tax ?? $validated['tax'] ?? 0);
        $otherFee = (int) ($transaction?->other_fee ?? $validated['other_fee'] ?? 0);
        $grandTotal = (int) ($transaction?->grand_total ?? $validated['grand_total'] ?? $subtotal - $discount + $tax + $otherFee);
        $paidAmount = (int) ($transaction?->paid_amount ?? $validated['paid_amount'] ?? $grandTotal);
        $changeAmount = (int) ($transaction?->change_amount ?? $validated['change_amount'] ?? max(0, $paidAmount - $grandTotal));
        $paymentMethodLabel = $transaction?->payment_method ?? $validated['payment_method'] ?? 'cash';

        $this->paperWidth = (int) config('app.printer_width', env('POS_PRINTER_WIDTH', 32));
        if (! in_array($this->paperWidth, [32, 48])) {
            $this->paperWidth = 32;
        }

        $printerName = $this->normalizePrinterName(config('app.printer_name', env('POS_PRINTER_NAME', 'VSC MP-80 Pro')));

        if (! $this->printerExists($printerName)) {
            return response()->json([
                'success' => false,
                'message' => "Printer '{$printerName}' tidak terpasang atau tidak terdeteksi di sistem.",
            ], 500);
        }

        try {
            $connector = new WindowsPrintConnector($printerName);
            $printer = new Printer($connector);

            $w = $this->paperWidth;

            // ESC/POS tuning for thermal print clarity
            $connector->write("\x1B\x7E\x04");
            $connector->write("\x1B\x37\x07\x80\x02");
            $printer->setEmphasis(true);
            $printer->setDoubleStrike(false);
            $printer->setJustification(Printer::JUSTIFY_CENTER);

            $printer->setTextSize(2, 2);
            $printer->text(strtoupper((string) config('app.name', 'SABU JAYA MART')) . "\n");
            $printer->setTextSize(1, 1);
            $printer->text("Struk Pembayaran\n");
            $printer->text(str_repeat('-', $w) . "\n");

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text($this->row('No. Transaksi', $transactionNumber, $w) . "\n");
            $printer->text($this->row('Tanggal', now()->format('d/m/Y, H.i.s'), $w) . "\n");
            $printer->text($this->row('Kasir', auth()->user()?->name ?? 'Kasir SABU JAYA MART', $w) . "\n");
            $printer->text($this->row('Pelanggan', $customerName, $w) . "\n");
            $printer->text(str_repeat('-', $w) . "\n");

            foreach ($items as $item) {
                $qty = (int) ($item['qty'] ?? 0);
                $price = (int) ($item['price'] ?? 0);
                $lineSubtotal = $qty * $price;
                $productName = trim((string) ($item['name'] ?? 'Barang'));
                $printer->text($this->truncateText($productName, $w) . "\n");
                $detailLine = $qty . ' x ' . $this->formatCurrency($price);
                $printer->text($this->row($detailLine, $this->formatCurrency($lineSubtotal), $w) . "\n");
            }

            $printer->text(str_repeat('-', $w) . "\n");
            $printer->text($this->row('Subtotal', $this->formatCurrency($subtotal), $w) . "\n");
            $printer->text($this->row('Diskon', $this->formatCurrency($discount), $w) . "\n");
            $printer->text($this->row('Pajak', $this->formatCurrency($tax), $w) . "\n");
            $printer->text($this->row('Biaya lain', $this->formatCurrency($otherFee), $w) . "\n");
            $printer->text(str_repeat('-', $w) . "\n");

            $printer->setEmphasis(true);
            $printer->setTextSize(1, 2);
            $printer->text($this->row('TOTAL', $this->formatCurrency($grandTotal), $w) . "\n");
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);
            $printer->text(str_repeat('-', $w) . "\n");

            $printer->text($this->row('Bayar', $this->formatCurrency($paidAmount), $w) . "\n");
            $printer->text($this->row('Kembali', $this->formatCurrency($changeAmount), $w) . "\n");
            $printer->text($this->row('Metode pembayaran', strtoupper((string) $paymentMethodLabel), $w) . "\n");
            $printer->text(str_repeat('-', $w) . "\n");

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("Terima kasih telah berbelanja.\n");
            $printer->text("Simpan struk ini sebagai bukti pembayaran.\n");
            $printer->setEmphasis(false);
            $printer->feed(4);
            $printer->cut(Printer::CUT_PARTIAL);
            $printer->close();

            return response()->json([
                'success' => true,
                'message' => 'Struk berhasil dicetak.',
                'transaction' => $transaction,
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mencetak struk', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'printer' => $printerName]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mencetak struk: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function row(string $left, string $right, int $width): string
    {
        $left = trim((string) $left);
        $right = trim((string) $right);

        if ($right === '') {
            return $this->truncateText($left, $width);
        }

        $rightLen = mb_strlen($right);
        $safeLeftWidth = max(1, $width - $rightLen - 2);
        $leftText = $this->truncateText($left, $safeLeftWidth);
        $gap = max(1, $width - (mb_strlen($leftText) + $rightLen));

        return $leftText . str_repeat(' ', $gap) . $right;
    }

    private function padRight(string $text, int $width): string
    {
        $text = trim((string) $text);
        $len = mb_strlen($text);
        if ($len >= $width) {
            return $this->truncateText($text, $width);
        }
        return $text . str_repeat(' ', $width - $len);
    }

    private function truncateText(string $text, int $length): string
    {
        if ($length <= 0) {
            return '';
        }
        return mb_strimwidth($text, 0, $length, '');
    }

    private function formatCurrency(int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    private function normalizePrinterName(?string $printerName): string
    {
        $printerName = trim((string) ($printerName ?? 'VSC MP-80 Pro'));
        if ($printerName === '') {
            return 'VSC MP-80 Pro';
        }
        return preg_replace('/\s+/', ' ', $printerName);
    }

    private function printerExists(string $printerName): bool
    {
        if (stripos(PHP_OS, 'WIN') !== 0) {
            return false;
        }

        $output = [];
        $result = 0;
        exec('wmic printer get name 2>NUL', $output, $result);

        if ($result !== 0 || empty($output)) {
            $output = [];
            exec('powershell -NoProfile -Command "Get-Printer | Select-Object -ExpandProperty Name" 2>NUL', $output, $result);
        }

        if ($result !== 0 || empty($output)) {
            return false;
        }

        foreach ($output as $line) {
            $line = trim((string) $line);
            if ($line === '' || stripos($line, 'Name') === 0) {
                continue;
            }
            if (strcasecmp($line, $printerName) === 0 || strcasecmp(str_replace('  ', ' ', $line), $printerName) === 0) {
                return true;
            }
        }

        return collect($output)
            ->map(fn ($line) => trim((string) $line))
            ->filter(fn ($line) => $line !== '')
            ->contains(fn ($line) => strcasecmp($line, $printerName) === 0 || strcasecmp($this->normalizePrinterName($line), $printerName) === 0);
    }
}