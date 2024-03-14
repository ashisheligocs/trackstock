<?php

namespace Modules\Restaurant\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RestroItem extends Model
{
    use HasFactory;
    protected $table = 'restro_items';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'order_id', 'restaurant_item_id', 'optional_item_ids', 'qty'];

    protected $casts = [
        'optional_item_ids' => 'array'
    ];

    public function restaurantItem()
    {
        return $this->belongsTo(RestaurantItem::class, 'restaurant_item_id');
    }
    
}


