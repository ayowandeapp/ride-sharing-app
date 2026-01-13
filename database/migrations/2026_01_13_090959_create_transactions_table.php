<?php

use App\Models\Trip;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Trip::class);
            $table->string('payment_method')->nullable();
            $table->decimal('total_cost', 8, 2)->nullable();
            $table->string('currency')->nullable();
            $table->decimal('amount_paid', 8, 2)->nullable();
            $table->string('status')->default('PENDING');
            $table->json('stripe_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
