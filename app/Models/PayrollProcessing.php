<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollProcessing extends Model
{
    protected $fillable = [
        'month',
        'year',
        'pdf_path',
        'processed_at',
        'total_processed',
        'total_sent',
        'status'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'month' => 'integer',
        'year' => 'integer',
    ];
}
