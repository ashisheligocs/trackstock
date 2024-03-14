<?php

namespace Modules\Restaurant\Entities;

use App\Models\VatRate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RestaurantItem extends Model
{
    use HasFactory;
    protected $table = 'restaurants_item';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'restaurant_id', 'item_id', 'varient_id','price', 'active'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function variant()
    {
        return $this->belongsTo(Varient::class, 'varient_id');
    }

    public function taxRate()
    {
       return $this->restaurant->taxes()->sum('rate');
    }
}
