<?php
// app/Models/TrainingBlock.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingBlock extends Model
{
    protected $fillable = ['training_card_id','label','badge_classes','sort_order'];

    public function card()
    {
        return $this->belongsTo(TrainingCard::class);
    }

    public function items()
    {
        return $this->hasMany(TrainingItem::class)->orderBy('sort_order');
    }
}
