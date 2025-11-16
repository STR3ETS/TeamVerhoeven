<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingSection extends Model
{
    protected $fillable = ['name', 'sort_order'];

    public function cards()
    {
        return $this->hasMany(TrainingCard::class)->orderBy('sort_order');
    }

    protected static function booted()
    {
        static::deleting(function (TrainingSection $section) {
            // let op: relation ->cards (collection), niet ->cards()
            $section->cards->each->delete();
        });
    }
}
