<?php

namespace Modules\Shops\Entities;

use App\Models\Scopes\selectedHotelResource;
use App\Models\VatRate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Room extends Model
{
    use HasFactory;
    protected $table = 'rooms';
    protected $primaryKey = 'id';
    // protected $fillable = ['id', 'room_name', 'room_categorary', 'hotel_id', 'price', 'bed_type_id', 'roomdescription', 'no_of_person', 'extra_person_capacity', 'extra_bed_capacity', 'no_of_child', 'del_status', 'room_rate', 'room_status', 'extra_bed_rate', 'per_person', 'no_of_infant', 'floor_id'];
    protected $fillable = ['id', 'room_name', 'room_categorary', 'hotel_id','bed_type_id', 'roomdescription','no_of_person','no_of_child', 'del_status', 'room_status','no_of_infant', 'floor_id','hotel_room_category_id'];

    public function Roomcategory()
    {
        return $this->belongsTo(Roomcategory::class, 'room_categorary');
    }
    public function hotelRoomCategory()
    {
        return $this->belongsTo(HotelRoomCategory::class, 'hotel_room_category_id');
    }
    public function FacilityName()
    {
        return $this->belongsTo(FacilityName::class, 'room_facilitytdatas');
    }
    public function Hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }
    public function floor()
    {
        return $this->belongsTo(Floor::class, 'floor_id');
    }

    public function Bed()
    {
        return $this->belongsTo(Bed::class, 'bed_type_id');
    }

    // public function Roomfacilities()
    // {
    //     return $this->belongsToMany(Roomfacility::class, 'room_facilitytdatas', 'roomsId', 'facilityId');
    // }

    public function bookingDetails($startDate = null, $endDate = null)
    {
        if (empty($startDate && $endDate)) {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }
        return $this->hasMany(BookingDetails::class)->whereBetween('created_at', [$startDate, $endDate]);
    }

    // public function taxRate()
    // {
    //     return $this->hasMany(RoomTax::class, 'room_id');
    // }

    // public function taxName()
    // {
    //     return $this->belongsToMany(VatRate::class, 'room_tax','room_id','tax_id');
    // }

    public function scopeAvailable($query, $date = null)
    {
        return $query->whereDoesntHave('roomCheckInOut')->orWhereHas('roomCheckInOut', function ($q) use ($date) {
           $q->whereIn('booking_status', [1, 4])->orWhereHas('booking', function ($bookingQuery) use ($date){
              $bookingQuery->whereDate('check_out_date', '<', $date ?? Carbon::now())->whereNotIn('booking_status_main', [Booking::CHECKIN, Booking::BOOKED]);
           });
        });

        // return $query->orWhereHas('roomCheckInOut', function ($q) use ($date) {
        //     $q->whereIn('booking_status', [1, 4])->whereNot('booking_status',5)
        //     ->whereHas('booking', function ($bookingQuery) use ($date){
        //        $bookingQuery->whereDate('check_out_date', '<', $date ?? Carbon::now())->whereNotIn('booking_status_main', [Booking::CHECKIN, Booking::BOOKED]);
        //     });
        //  });

    }

   

    public function scopeOccupied($query, $date = null)
    {
        return $query->whereHas('roomCheckInOut', function ($q) use ($date) {
            $q->whereNotIn('booking_status', [1, 4])->whereHas('booking', function ($bookingQuery) use ($date){
                $bookingQuery->whereDate('check_in_date', '<=', $date ?? Carbon::now())->whereDate('check_out_date', '>=', $date ?? Carbon::now())->whereIn('booking_status_main', [Booking::CHECKIN, Booking::BOOKED]);
            });
        });
    }

    public function scopeRoomcheckin($query, $date = null)
    {
        return $query->whereHas('roomCheckInOut', function ($q) use ($date) {
            $q->whereNotIn('booking_status', [1, 4])->whereHas('booking', function ($bookingQuery) use ($date){
                $bookingQuery->whereDate('check_out_date', '>=', $date ?? Carbon::now())->whereIn('booking_status_main', [Booking::CHECKIN]);
            });
        });
    }

    public function roomCheckInOut()
    {
        return $this->hasMany(BookingDetails::class, 'room_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SelectedHotelResource);
    }
}
