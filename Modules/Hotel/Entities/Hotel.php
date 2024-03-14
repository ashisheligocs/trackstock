<?php

namespace Modules\Hotel\Entities;

use App\Models\GeneralSetting;
use App\Models\Scopes\SelectedHotel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use \Modules\Restaurant\Entities\Restaurant;

class Hotel extends Model
{
    use HasFactory;
    protected $table = 'hotels';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'hotel_name', 'hotel_address', 'hotelcategory_id', 'hotel_phone1', 'hotel_phone',
                           'hotel_email', 'total_no_of_rooms', 'hotel_Status', 'no_of_floor', 'contact_phone',
                            'contact_name', 'del_status', 'images','state','city','image_path','hotel_prefix'];

    public function category()
    {
        return $this->belongsTo(Hotelcategory::class, 'hotelcategory_id');
    }

    public function facilities()
    {
        // return $this->belongsToMany(Hotelfacility::class, 'hotel_facility_hotel', 'hotel_id', 'facility_id');
        return $this->belongsTo(Roomfacility::class, 'facilityId');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_hotel');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function roomFacilityData()
    {
        return $this->hasMany(RoomFacilitytdata::class);
    }
    

    public function hotelroomcategory()
    {
        return $this->hasMany(HotelRoomCategory::class);
    }
   

    public function restaurant()
    {
        return $this->hasMany(Restaurant::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SelectedHotel);
    }
}
