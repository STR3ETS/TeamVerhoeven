<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'coach_id',
        'birthdate',
        'gender',
        'address',
        'height_cm',
        'weight_kg',
        'goals',
        'injuries',
        'period_weeks',
        'frequency',
        'background',
        'facilities',
        'materials',
        'work_hours',
        'heartrate',
        'test_12min',
        'test_5k',
        'coach_preference',
    ];

    protected $casts = [
        'birthdate'      => 'date',
        'address'        => 'array',
        'height_cm'      => 'decimal:2',
        'weight_kg'      => 'decimal:2',
        'goals'          => 'array',
        'injuries'       => 'array',
        'period_weeks'   => 'integer',
        'frequency'      => 'array',
        'heartrate'      => 'array',
        'test_12min'     => 'array',
        'test_5k'        => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }
}
