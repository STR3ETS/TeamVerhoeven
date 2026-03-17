<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyCheckin extends Model
{
    protected $fillable = [
        'user_id', 'week_number',
        'recovery', 'recovery_note',
        'training_feel', 'training_feel_note',
        'sleep_quality', 'sleep_quality_note',
        'stress', 'stress_note',
        'progression', 'progression_note',
        'soreness', 'soreness_note',
        'motivation', 'motivation_note',
    ];

    protected $casts = [
        'recovery' => 'integer',
        'training_feel' => 'integer',
        'sleep_quality' => 'integer',
        'stress' => 'integer',
        'progression' => 'integer',
        'soreness' => 'integer',
        'motivation' => 'integer',
        'week_number' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function average(): float
    {
        return round(($this->recovery + $this->training_feel + $this->sleep_quality
            + $this->stress + $this->progression + $this->soreness + $this->motivation) / 7, 1);
    }
}
