<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntakeStepLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'intake_id',
        'step',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function intake()
    {
        return $this->belongsTo(Intake::class);
    }
}
