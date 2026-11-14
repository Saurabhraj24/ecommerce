<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'punch_in_at',
        'punch_out_at',
        'status',
        'total_hours',
        'punch_in_ip',
        'punch_out_ip',
        'verification_method',
        'device_id',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'total_hours' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate and update total hours worked between punch in and punch out.
     */
    public function calculateTotalHours(): void
    {
        if ($this->punch_in_at && $this->punch_out_at) {
            $in = Carbon::parse($this->punch_in_at);
            $out = Carbon::parse($this->punch_out_at);
            if ($out->greaterThan($in)) {
                $hours = $out->diffInMinutes($in) / 60;
                $this->total_hours = round($hours, 2);
            }
        }
    }
}
