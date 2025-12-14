<?php

namespace App\Services\Payments\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Contracts\PaymentResult;
use App\Models\Order;

class CreditCardGateway implements PaymentGatewayInterface
{
    public function pay(Order $order, array $payload): PaymentResult
    {
        return new PaymentResult(
            success: true,
            status: 'successful',
            transactionId: 'CC-' . uniqid(),
            message: 'Payment processed via Credit Card',
            meta: ['provider' => 'credit_card']
        );
    }
}
