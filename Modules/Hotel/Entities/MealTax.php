<?php

namespace Modules\Hotel\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\VatRate;

class MealTax extends Model
{
    use HasFactory;
    protected $table = 'meal_taxs';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'tax_id', 'meal_price_id'];

    public function taxName()
    {
        return $this->belongsTo(VatRate::class, 'tax_id');
    }
}
