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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('billed_by_id')->nullable()->after('user_id')->constrained('users')->onDelete('set null');
            $table->string('cashier_name', 100)->nullable()->after('billed_by_id');
            $table->string('cashier_email', 150)->nullable()->after('cashier_name');
            $table->string('cashier_role', 50)->nullable()->after('cashier_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['billed_by_id']);
            $table->dropColumn(['billed_by_id', 'cashier_name', 'cashier_email', 'cashier_role']);
        });
    }
};
