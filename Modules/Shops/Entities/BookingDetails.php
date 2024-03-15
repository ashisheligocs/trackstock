<?php

namespace Modules\Shops\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingDetails extends Model
{
    use HasFactory;
    protected $table = 'booking_details';
    protected $primaryKey = 'id';
    protected $fillable = ['id','booking_id','meal_plan_id','room_id','adult','children','infant','extra_facility_days',
                           'extra_bed','extra_person','extra_child','complementary','room_no','room_rate','booking_status',
                           'room_tax','meal_plan_tax','facility_tax','extra_bed_tax','extra_person_tax','meal_plan_persons',
                           'modified_room_rate','extra_bed_price','extra_person_price'];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function complementrays()
    {
        return $this->hasMany(BookingComplementray::class, 'booking_Detail_id');
    }
    public function mealPlanDetails()
    {
        return $this->belongsTo(HotelMealPlan::class, 'meal_plan_id');
    }

    public function getStatusName()
    {
        switch ($this->booking_status) {
            case '0':
                $status =  '<span class="badge badge-warning float-right">Hold</spnan>';
                break;
            case '1':
                $status =  '<span class="badge badge-warning float-right">Cancel</spnan>';
                break;
            case '2':
                $status =  '<span class="badge badge-success float-right config_textbtn">Booked</spnan>';
                break;
            case '3':
                $status =  '<span class="badge badge-warning float-right">Checkin</spnan>';
                break;
            case '4':
                $status =  '<span class="badge badge-warning float-right">Checkout</spnan>';
                break;
            default:
                $status =  '<span class="badge badge-primary float-right config_textbtn">Pending Payment</spnan>';
                break;
        }
        return $status;
    }
}
