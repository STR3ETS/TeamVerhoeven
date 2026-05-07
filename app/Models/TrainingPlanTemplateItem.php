<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingPlanTemplateItem extends Model
{
    protected $fillable = ['template_id', 'week', 'day', 'training_card_id', 'sort_order'];

    public function template()
    {
        return $this->belongsTo(TrainingPlanTemplate::class, 'template_id');
    }

    public function card()
    {
        return $this->belongsTo(TrainingCard::class, 'training_card_id');
    }
}
