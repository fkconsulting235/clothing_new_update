<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name', 120);
            $table->string('customer_phone', 30);
            $table->string('customer_email')->nullable();
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->string('payment_proof_path')->nullable();
            $table->decimal('total', 10, 2)->default(0);
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
