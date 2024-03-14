<?php

namespace Modules\Restaurant\Entities;

use App\Models\VatRate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemTax extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'restaurant_id' ,'tax_id'];
    protected $primaryKey = 'id';

    public function taxName()
    {
        return $this->belongsTo(VatRate::class, 'tax_id');
    }
}
