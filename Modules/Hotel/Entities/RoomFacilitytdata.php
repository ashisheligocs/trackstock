<?php

namespace Modules\Hotel\Entities;

use App\Models\VatRate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoomFacilitytdata extends Model
{
    use HasFactory;

    protected $table = 'room_facilitytdatas';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'hotel_id', 'facilityId', 'status', "price"];

    public function facilityName()
    {
        return $this->belongsTo(Roomfacility::class, 'facilityId');
    }

    public function taxName()
    {
        return $this->belongsToMany(VatRate::class, 'facility_taxs', 'facility_id', 'tax_id');
    }

    public function taxRate()
    {
        return $this->hasMany(FacilityTax::class, 'facility_id');
    }

    public function totalTaxRate()
    {
        if (!empty($this->taxName)) {
            return $this->taxName()->sum('rate');
        }
    }
}
