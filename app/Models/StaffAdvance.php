<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffAdvance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'advance_date',
        'reason',
        'status',
        'deducted_in_salary_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'advance_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function salary(): BelongsTo
    {
        return $this->belongsTo(StaffSalary::class, 'deducted_in_salary_id');
    }
}
