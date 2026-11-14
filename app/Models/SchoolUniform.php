<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolUniform extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'school_name',
        'class_grade',
        'gender',
        'item_type',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
