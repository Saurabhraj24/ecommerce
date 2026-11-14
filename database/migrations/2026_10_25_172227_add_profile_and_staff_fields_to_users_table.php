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
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('phone');
            $table->string('employee_id', 50)->nullable()->unique()->after('avatar');
            $table->string('designation', 100)->nullable()->after('employee_id');
            $table->string('department', 100)->nullable()->after('designation');
            $table->date('date_of_joining')->nullable()->after('department');
            $table->text('address')->nullable()->after('date_of_joining');
            $table->string('emergency_contact_name', 100)->nullable()->after('address');
            $table->string('emergency_contact_phone', 20)->nullable()->after('emergency_contact_name');
            $table->decimal('salary', 10, 2)->nullable()->after('emergency_contact_phone');
            $table->text('notes')->nullable()->after('salary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar',
                'employee_id',
                'designation',
                'department',
                'date_of_joining',
                'address',
                'emergency_contact_name',
                'emergency_contact_phone',
                'salary',
                'notes',
            ]);
        });
    }
};
