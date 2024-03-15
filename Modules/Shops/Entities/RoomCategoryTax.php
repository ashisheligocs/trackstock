<?php

namespace Modules\Shops\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\VatRate;
class RoomCategoryTax extends Model
{
    use HasFactory;

    protected $table = 'room_category_tax';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'tax_id', 'room_category_id'];

    public function taxName()
    {
        return $this->belongsTo(VatRate::class, 'tax_id');
    }
}
