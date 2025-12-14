<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use InvalidArgumentException;

class GatewayResolver
{
    public function resolve(string $method): PaymentGatewayInterface
    {
        $map = config('payment.gateways');

        if (! isset($map[$method])) {
            throw new InvalidArgumentException("Unsupported payment method [$method]");
        }

        return app($map[$method]);
    }
}
