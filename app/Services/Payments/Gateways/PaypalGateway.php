<?php

namespace App\Services\Payments\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Contracts\PaymentResult;
use App\Models\Order;

class PaypalGateway implements PaymentGatewayInterface
{
    public function pay(Order $order, array $payload): PaymentResult
    {
        return new PaymentResult(
            success: true,
            status: 'successful',
            transactionId: 'PAYPAL-' . uniqid(),
            message: 'Payment processed via PayPal',
            meta: ['provider' => 'paypal']
        );
    }
}
