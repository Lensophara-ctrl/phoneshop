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
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['phone_id']);
            $table->dropColumn(['phone_id', 'qty']);
            $table->decimal('subtotal', 10, 2)->after('user_id')->nullable();
            $table->decimal('tax', 10, 2)->after('subtotal')->default(0);
            $table->string('payment_method')->after('total_price')->default('cash');
            $table->string('status')->after('payment_method')->default('completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('phone_id')->constrained()->onDelete('cascade');
            $table->integer('qty')->after('phone_id');
            $table->dropColumn(['subtotal', 'tax', 'payment_method', 'status']);
        });
    }
};
