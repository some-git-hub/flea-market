<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'postal_code',
        'address',
        'building',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
