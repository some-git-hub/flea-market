<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'reviewer_id',
        'reviewee_id',
        'rating',
    ];

    // 評価された取引
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // 評価する人
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // 評価される人
    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }
}
