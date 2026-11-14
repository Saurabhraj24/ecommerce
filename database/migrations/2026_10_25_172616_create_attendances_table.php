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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->time('punch_in_at')->nullable();
            $table->time('punch_out_at')->nullable();
            $table->enum('status', ['present', 'late', 'half_day', 'absent', 'leave'])->default('present');
            $table->decimal('total_hours', 5, 2)->default(0);
            $table->string('punch_in_ip', 45)->nullable();
            $table->string('punch_out_ip', 45)->nullable();
            $table->string('verification_method', 30)->default('dynamic_qr');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'date']);
            $table->index(['date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
