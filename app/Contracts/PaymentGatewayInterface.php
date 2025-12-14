<?php

namespace App\Contracts;

use App\Models\Order;

interface PaymentGatewayInterface
{
    public function pay(Order $order, array $payload): PaymentResult;
}
