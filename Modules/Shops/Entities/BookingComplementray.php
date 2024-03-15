<?php

namespace Modules\Shops\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingComplementray extends Model
{
    use HasFactory;

    protected $table = 'booking_complementrays';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'booking_Detail_id', 'complementary_id','quantity','modified_rate'];

    
    public function paidFacility()
    {
        return $this->belongsTo(Roomfacility::class,'complementary_id');
    }
}
