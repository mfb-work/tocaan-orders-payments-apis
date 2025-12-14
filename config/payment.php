<?php

return [
    'gateways' => [
        'paypal'      => \App\Services\Payments\Gateways\PaypalGateway::class,
        'credit_card' => \App\Services\Payments\Gateways\CreditCardGateway::class,
        'new_gateway' => \App\Services\Payments\Gateways\NewGateway::class, 
    ],
];
