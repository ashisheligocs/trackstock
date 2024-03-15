<?php

namespace Modules\Shops\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MealPlan extends Model
{
    use HasFactory;

    protected $table = 'meal_plans';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'del_status', 'plan_name','short_name'];


    public function rateHotel()
    {
        return $this->hasMany(HotelMealPlan::class,'meal_id');
    }
}
