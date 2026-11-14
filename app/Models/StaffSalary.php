<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaffSalary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'month',
        'base_salary',
        'working_days',
        'days_present',
        'days_absent',
        'days_half',
        'paid_leaves',
        'bonus_incentive',
        'deductions',
        'net_salary',
        'payment_status',
        'payment_method',
        'payment_reference',
        'payment_date',
        'notes',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'days_present' => 'decimal:1',
        'days_absent' => 'decimal:1',
        'days_half' => 'decimal:1',
        'paid_leaves' => 'decimal:1',
        'bonus_incentive' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function advances(): HasMany
    {
        return $this->hasMany(StaffAdvance::class, 'deducted_in_salary_id');
    }

    public function getMonthLabelAttribute(): string
    {
        return \Carbon\Carbon::createFromFormat('Y-m', $this->month)->format('F Y');
    }
}
