<?php

namespace Modules\Shops\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\VatRate;

class FacilityTax extends Model
{
    use HasFactory;

    protected $table = 'facility_taxs';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'tax_id', 'facility_id'];

    public function taxName()
    {
        return $this->belongsTo(VatRate::class, 'tax_id');
    }
}
