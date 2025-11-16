<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingCard extends Model
{
    protected $fillable = ['training_section_id', 'title', 'sort_order'];

    public function section()
    {
        return $this->belongsTo(TrainingSection::class);
    }

    public function blocks()
    {
        return $this->hasMany(TrainingBlock::class)->orderBy('sort_order');
    }

    // extra: link naar assignments zodat die ook weg gaan bij verwijderen
    public function assignments()
    {
        return $this->hasMany(TrainingAssignment::class);
    }

    protected static function booted()
    {
        static::deleting(function (TrainingCard $card) {
            // alle blocks + items
            $card->blocks->each->delete();   // NIET ->blocks()

            // alle geplande trainingen die deze card gebruiken
            $card->assignments()->delete();  // directe delete op query
        });
    }
}
