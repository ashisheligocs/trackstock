<?php

namespace Modules\Restaurant\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;
    protected $table = 'items';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'order_id','category_id', 'item_name' , 'item_image' , 'description', 'notes', 'status'];

    public function foodCategory(){
        return $this->belongsTo(FoodCategory::class, 'category_id');
    }
    
    public function itemPrice()
    {
        return $this->hasMany(Itemprice::class);
    }

    public function restaurantItemPrice()
    {
        return $this->hasMany(RestaurantItem::class);
    }

    public function optionalItems()
    {
        return $this->hasMany(OptionalItem::class);
    }

    public function optionalItemsPrices()
    {
        return $this->hasMany(OptionalItemPrice::class,'item_id','id');
    }

    public function restaurant()
    {
        return $this->hasOneThrough(Restaurant::class, RestaurantItem::class);
    }
}
