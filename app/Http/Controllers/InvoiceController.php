<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InvoiceController extends Controller
{
    public function download(Request $request, Transaction $transaction): Response
    {
        $user = $request->user();
        abort_unless(
            $user->isAdmin() || ($user->isCashier() && $transaction->user_id === $user->id),
            403
        );

        $transaction->load(['details', 'user']);

        return Pdf::loadView('invoices.transaction', compact('transaction'))
            ->setPaper('a4')
            ->download('invoice-'.$transaction->transaction_number.'.pdf');
    }
}
