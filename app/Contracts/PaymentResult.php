<?php

namespace App\Contracts;

class PaymentResult
{
    public function __construct(
        public bool $success,
        public string $status, // successful | failed
        public ?string $transactionId = null,
        public ?string $message = null,
        public array $meta = [],
    ) {}
}
