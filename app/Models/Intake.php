<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intake extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'status',
        'payload',
        'completed_at',
    ];

    protected $casts = [
        'payload'      => 'array',
        'completed_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function logs()
    {
        return $this->hasMany(IntakeStepLog::class);
    }
}
