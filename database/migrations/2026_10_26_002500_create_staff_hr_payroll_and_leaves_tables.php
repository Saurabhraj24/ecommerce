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
        // Add banking, shift & weekly off details to users
        Schema::table('users', function (Blueprint $table) {
            $table->string('bank_name', 100)->nullable()->after('salary');
            $table->string('bank_account_no', 50)->nullable()->after('bank_name');
            $table->string('bank_ifsc', 25)->nullable()->after('bank_account_no');
            $table->string('upi_id', 75)->nullable()->after('bank_ifsc');
            $table->string('weekly_off', 20)->default('Sunday')->after('upi_id');
            $table->string('shift_timings', 50)->default('10:00 AM - 08:30 PM')->after('weekly_off');
        });

        // Staff Leaves (Chutti & Vacation)
        Schema::create('staff_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('leave_type', 30)->default('casual'); // casual, sick, festival, unpaid, emergency
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('days_count', 4, 1)->default(1.0);
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('approved');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });

        // Staff Monthly Salary & Payroll (वेतन रजिस्टर)
        Schema::create('staff_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('month', 7); // e.g. 2026-10
            $table->decimal('base_salary', 10, 2)->default(0);
            $table->integer('working_days')->default(30);
            $table->decimal('days_present', 4, 1)->default(0);
            $table->decimal('days_absent', 4, 1)->default(0);
            $table->decimal('days_half', 4, 1)->default(0);
            $table->decimal('paid_leaves', 4, 1)->default(0);
            $table->decimal('bonus_incentive', 10, 2)->default(0); // Festive bonus, sales commission
            $table->decimal('deductions', 10, 2)->default(0); // Absent penalty, advance deduction
            $table->decimal('net_salary', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'paid', 'partially_paid'])->default('pending');
            $table->string('payment_method', 30)->nullable(); // cash, upi, bank_transfer, cheque
            $table->string('payment_reference', 100)->nullable(); // UTR / Txn No
            $table->date('payment_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'month']);
            $table->index(['month', 'payment_status']);
        });

        // Staff Salary Advances & Peshgi Khata (पेशगी)
        Schema::create('staff_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('advance_date');
            $table->string('reason');
            $table->enum('status', ['pending', 'deducted_from_salary', 'cash_repaid'])->default('pending');
            $table->foreignId('deducted_in_salary_id')->nullable()->constrained('staff_salaries')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_advances');
        Schema::dropIfExists('staff_salaries');
        Schema::dropIfExists('staff_leaves');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bank_name',
                'bank_account_no',
                'bank_ifsc',
                'upi_id',
                'weekly_off',
                'shift_timings',
            ]);
        });
    }
};
