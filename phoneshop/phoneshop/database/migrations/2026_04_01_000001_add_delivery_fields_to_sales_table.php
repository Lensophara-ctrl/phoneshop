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
            $table->decimal('delivery_latitude', 10, 8)->nullable()->after('customer_postal_code');
            $table->decimal('delivery_longitude', 11, 8)->nullable()->after('delivery_latitude');
            $table->string('delivery_status')->default('pending')->after('status');
            $table->timestamp('delivery_estimated_at')->nullable()->after('delivery_status');
            $table->timestamp('delivery_completed_at')->nullable()->after('delivery_estimated_at');
            $table->string('delivery_driver_name')->nullable()->after('delivery_completed_at');
            $table->string('delivery_driver_phone')->nullable()->after('delivery_driver_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_latitude',
                'delivery_longitude',
                'delivery_status',
                'delivery_estimated_at',
                'delivery_completed_at',
                'delivery_driver_name',
                'delivery_driver_phone'
            ]);
        });
    }
};
