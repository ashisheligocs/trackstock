<?php

namespace Modules\Shops\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Roomfacility extends Model
{
    use HasFactory;
    protected $table = 'room_facility';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'del_status', 'room_facility_title', 'facility_description','facility_img', 'price'];


    public function Rooms()
    {
        return $this->belongsToMany(Hotel::class, 'room_facilitytdatas', 'facilityId', 'hotel_id');
    }

    public function facilityRate()
    {
        return $this->hasMany(RoomFacilitytdata::class,'facilityId');
    }
    
}
