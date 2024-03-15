<?php

namespace Modules\Shops\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hotelfacilityhotel extends Model
{
    use HasFactory;
    protected $table = 'hotel_facility_hotel';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'hotel_id', 'facility_id'];
    
}
