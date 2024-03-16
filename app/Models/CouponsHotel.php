<?php

namespace App\Models;
use App\Models\Scopes\selectedHotelResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Shops\Entities\Shop;

class CouponsHotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id', 'coupon_id'
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
    
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SelectedHotelResource);
    }
}
