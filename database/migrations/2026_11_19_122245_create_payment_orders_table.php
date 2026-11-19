<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('purpose'); // ticket, subscription, topup
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('target_code')->nullable();
            $table->string('item_name');
            $table->unsignedBigInteger('amount');
            $table->string('payment_method');
            $table->string('status')->default('pending'); // pending, paid, failed, expired, cancelled
            $table->string('snap_token')->nullable();
            $table->string('snap_redirect_url')->nullable();
            $table->string('gateway_transaction_id')->nullable();
            $table->string('gateway_payment_type')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_orders');
    }
};
