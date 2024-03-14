<?php

namespace Modules\Restaurant\Entities;

use App\Models\VatRate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Restaurant extends Model
{
    use HasFactory;
    protected $table = 'restaurants';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'hotel_id'];

    public function hotel() : BelongsTo
    {
        return $this->belongsTo(\Modules\Hotel\Entities\Hotel::class, 'hotel_id');
    }

    public function price(): HasMany
    {
        return $this->hasMany(RestaurantItem::class);
    }

    public function taxes()
    {
        return $this->belongsToMany(VatRate::class, 'item_taxes', 'restaurant_id', 'tax_id');
    }

    public function restaurantItems()
    {
        return $this->hasMany(RestaurantItem::class);
    }
}
