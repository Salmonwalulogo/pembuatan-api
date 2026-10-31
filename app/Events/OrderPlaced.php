<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $transactionNumber,
        public int $grandTotal,
        public string $paymentMethod,
        public string $cashierName,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('transactions')];
    }

    public function broadcastAs(): string
    {
        return 'order.placed';
    }

    public function broadcastWith(): array
    {
        return [
            'transaction_number' => $this->transactionNumber,
            'grand_total' => $this->grandTotal,
            'payment_method' => $this->paymentMethod,
            'cashier_name' => $this->cashierName,
        ];
    }
}
