<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(private readonly GatewayResolver $resolver) {}

    public function process(Order $order, string $method, array $payload): Payment
    {
        $gateway = $this->resolver->resolve($method);
        $result  = $gateway->pay($order, $payload);

        return DB::transaction(function () use ($order, $method, $result) {
            return Payment::create([
                'order_id'       => $order->id,
                'method'         => $method,
                'status'         => $result->status,
                'amount'         => $order->total_amount,
                'transaction_id' => $result->transactionId,
                'raw_response'   => $result->meta,
            ]);
        });
    }
}
