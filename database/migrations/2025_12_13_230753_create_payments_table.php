<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->restrictOnDelete(); // يمنع حذف الطلب لو له مدفوعات

            $table->string('method', 50);
            $table->enum('status', ['pending', 'successful', 'failed'])
                ->default('pending');

            $table->decimal('amount', 10, 2);

            $table->string('transaction_id')->nullable();
            $table->json('raw_response')->nullable();

            $table->timestamps();

            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
