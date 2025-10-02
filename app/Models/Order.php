<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'intake_id',
        'period_weeks',
        'amount_cents',
        'currency',
        'provider',
        'provider_ref',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'period_weeks' => 'integer',
        'amount_cents' => 'integer',
        'paid_at'      => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function intake()
    {
        return $this->belongsTo(Intake::class);
    }

    public function getAmountEuroAttribute(): float
    {
        return $this->amount_cents / 100;
    }
}
