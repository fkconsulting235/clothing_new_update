<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->string('customer_name', 120);
            $table->string('customer_phone', 30);
            $table->unsignedInteger('quantity')->default(1);
            $table->enum('status', ['active', 'expired', 'converted', 'cancelled'])->default('active');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index('status');
            $table->index('customer_phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
