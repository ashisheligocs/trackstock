<?php

namespace Modules\Shops\Entities;

use App\Models\VatRate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;

class HotelMealPlan extends Model
{
    use HasFactory;
    protected $table = 'hotel_meal_plans';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'hotel_id', 'meal_id', 'price', 'status'];

    public function mealname()
    {
        return $this->belongsTo(MealPlan::class, 'meal_id');
    }

    public function taxRate()
    {
        return $this->hasMany(MealTax::class, 'meal_price_id');
    }

    public function taxName()
    {
        return $this->belongsToMany(VatRate::class, 'meal_taxs', 'meal_price_id', 'tax_id');
    }

    public function totalTaxRate()
    {
        if (!empty($this->taxName)) {
            return $this->taxName()->sum('rate');
        }
    }
}
