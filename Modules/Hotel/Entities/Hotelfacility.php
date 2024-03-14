<?php

namespace Modules\Hotel\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hotelfacility extends Model
{
    use HasFactory;

    protected $table = 'hotels_facility';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'del_status', 'facility_title'];

    public function hotels()
    {
        return $this->belongsToMany(Hotel::class, 'hotel_facility_hotel', 'facility_id', 'hotel_id');
    }
}
