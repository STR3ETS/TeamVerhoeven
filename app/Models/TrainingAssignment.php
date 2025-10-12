<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingAssignment extends Model
{
    protected $fillable = ['user_id','training_card_id','day','sort_order','week'];

    public function client() { return $this->belongsTo(User::class, 'user_id'); }
    public function card()   { return $this->belongsTo(TrainingCard::class, 'training_card_id'); }
}