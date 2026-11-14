<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->json('images')->nullable()->after('comment');
            $table->string('fit_feedback')->nullable()->after('images');
            $table->string('quality_rating')->nullable()->after('fit_feedback');
            $table->boolean('verified_purchase')->default(true)->after('quality_rating');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['images', 'fit_feedback', 'quality_rating', 'verified_purchase']);
        });
    }
};
