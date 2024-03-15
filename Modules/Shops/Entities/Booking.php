<?php

namespace Modules\Shops\Entities;

use App\Models\AccountTransaction;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use DateTime;
use Modules\Restaurant\Entities\Restroorder;

class Booking extends Model
{
    use HasFactory;
    protected $table = 'bookings';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'arrival_from',
        'purpose',
        'booking_number',
        'customer_id',
        'total_room',
        'advance_amount',
        'paid_amount',
        'total_price',
        'offer_discount',
        'full_guest_name',
        'special_request',
        'comments',
        'booking_date',
        'check_in_date',
        'check_out_date',
        'hotel_id',
        'client_booking_status',
        'booking_source',
        'discount_reason',
        'discount_amount',
        'payment_method',
        'advance_remarks',
        'remarks',
        'booked_from',
        'discount_type',
        'booking_status_main',
        'final_gst_rates',
        'extra_charge',
        'extra_charge_comment',
        'special_discount_amount',
        'special_discount_type',
        'credit_amount',
        'credit_payment_method',
        'special_discount_rate',
        'remaining_amount',
        'invoice_id',
        'tax_included',
        'commission_to',
        'commission_amount'
    ];

    const FIXED = 1;
    const PERCENTAGE = 2;

    const HOLD = 0;
    const CANCEL = 1;
    const BOOKED = 2;
    const CHECKIN = 3;
    const CHECKOUT = 4;
    const PENDING_PAYMENT = 5;
    const PAST = 6;
    protected $casts = [
        'final_gst_rates' => 'array',
        'tax_included'    => 'boolean',
    ];
    // protected $casts = [
    //     'check_in_date' => 'date:d F Y',
    //     'check_out_date' => 'date:d F Y',
    // ];

    public function Customer()
    {
        return $this->belongsTo(\App\Models\Client::class, 'customer_id');
    }

    public function customers()
    {
        return $this->belongsToMany(\App\Models\Client::class, 'client_bookigs_details', 'booking_id', 'customer_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function restaurantOrders()
    {
        return $this->hasMany(Restroorder::class);
    }

    public function Source()
    {
        return $this->belongsTo(\App\Models\Client::class, 'booking_source');
    }

    public function BookingType()
    {
        return $this->belongsTo(BookingType::class, 'booking_type_id');
    }

    public function Hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function BookingDetails()
    {
        return $this->hasMany(BookingDetails::class);
    }

    public function accountTransactions()
    {
        return $this->hasMany(AccountTransaction::class);
    }

    public function dueAmount()
    {
        $paid = ($this->total_price) - ($this->paid_amount);
        $dueAmount = $this->paid_amount;
        if ($paid == 0) {
            return '<span class="badge badge-warning">UnPaid</spnan>';
        }
        if ($dueAmount > 0) {
            return '<span class="badge badge-danger">'.number_format($dueAmount).'</spnan>';
        }

        return '<span class="badge badge-success">Paid</spnan>';
    }

    public function totalGst()
    {
        if ($this->final_gst_rates && !empty($this->final_gst_rates)) {
            return array_sum(array_values($this->final_gst_rates));
        }

        return 0;
    }

    public function getStatusName()
    {
        switch ($this->client_booking_status) {
            case '1':
                $status = '<span class="badge badge-danger float-right config_textbtn">Pending</spnan>';
                break;
            case '3':
                $status = '<span class="badge badge-success float-right">Confirmed</spnan>';
                break;
            case '2':
                $status = '<span class="badge badge-warning float-right">Hold</spnan>';
                break;
            default:
                $status = '<span class="badge badge-primary float-right config_textbtn">Available</spnan>';
                break;
        }

        return $status;
    }

    public function getMainBookingStatusName(){
        switch ($this->booking_status_main) {
            case '1':
                $status = '<span class="badge badge-danger float-right">Cancel</spnan>';
                break;
            default:
                $status = '<span class="badge badge-primary float-right config_textbtn">Available</spnan>';
                break;
        }

        return $status;
    }

    public function bokingStatus()
    {
        return $this->belongsTo(BookingStatus::class, 'client_booking_status');
    }

    public function scopeFilter($query, $filter)
    {
        $query->when($filter['type'] ?? null, function ($query, $type) {
            $query->when($type == Booking::BOOKED, function ($q) {
                $q->whereDate('check_in_date', '>', Carbon::now())->whereIn('booking_status_main',
                    [Booking::BOOKED, Booking::PENDING_PAYMENT]);
            });
            $query->when($type == Booking::CHECKIN, function ($q) {
                $q->whereDate('check_in_date', '<=', Carbon::now())->whereDate('check_out_date', '>', Carbon::now())
                    ->whereIn('booking_status_main', [Booking::BOOKED, Booking::PENDING_PAYMENT]);
            });
            $query->when($type == Booking::PENDING_PAYMENT, function ($q) {
                $q->whereDate('check_out_date', '>=', Carbon::now())->where('booking_status_main', Booking::CHECKIN);
            });
            $query->when($type == Booking::CHECKOUT, function ($q) {
                $q->whereDate('check_out_date', '<=', Carbon::now());
            });
            $query->when($type == Booking::PAST, function ($q) {
                $q->where('booking_status_main', Booking::CHECKOUT);
            });
            $query->when($type == Booking::CANCEL, function ($q) {
                $q->where('booking_status_main', Booking::CANCEL);
            });
        });
    }

    public function discountType()
    {
        if ($this->discount_type == Booking::FIXED) {
            return ['title' => "Fixed", 'id' => 1];
        }
        if ($this->discount_type == Booking::PERCENTAGE) {
            return ['title' => "Percentage", 'id' => 2];
        }

        return [];
    }

    public function isChecking()
    {
        return Carbon::parse($this->check_in_date)->isPast();
    }
}
