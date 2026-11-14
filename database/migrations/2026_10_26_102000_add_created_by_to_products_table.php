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
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('created_by_id')->nullable()->after('meta_description')->constrained('users')->onDelete('set null');
            $table->string('creator_name', 100)->nullable()->after('created_by_id');
            $table->string('creator_email', 150)->nullable()->after('creator_name');
            $table->foreignId('updated_by_id')->nullable()->after('creator_email')->constrained('users')->onDelete('set null');
            $table->string('updater_name', 100)->nullable()->after('updated_by_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['created_by_id']);
            $table->dropForeign(['updated_by_id']);
            $table->dropColumn(['created_by_id', 'creator_name', 'creator_email', 'updated_by_id', 'updater_name']);
        });
    }
};
