<?php

namespace Modules\Restaurant\Entities;

use App\Models\AccountTransaction;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Hotel\Entities\Booking;
use Modules\Hotel\Entities\Hotel;
use Modules\Hotel\Entities\Room;

class Restroorder extends Model
{
    use HasFactory;
    protected $table = 'item_restro_orders';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'order_id_uniq','invoice_id', 'customer_id', 'order_date', 'order_status', 'total_amount', 'discount', 'tax', 'room_id', 'booking_id', 'hotel_id'];
    protected $appends = ['type'];
    public function items(){
        return $this->hasMany(RestroItem::class,'order_id');
    }

    public function hotel(){
        return $this->belongsTo(Hotel::class);
    }

    public function getTypeAttribute()
    {
        return $this->booking_id ? 'Room order' : 'Customer order';
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'customer_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function transactions()
    {
        return $this->hasMany(AccountTransaction::class, 'order_id');
    }

    public function getOrderStatusName()
    {
        switch ($this->order_status) {
            case '2':
                $status = '<span class="badge badge-danger float-right config_textbtn">Cancel</spnan>';
                break;
            case '1':
                $status = '<span class="badge badge-success float-right config_textbtn">Confirmed</spnan>';
                break;
            case '0':
                $status = '<span class="badge badge-warning float-right">Pending</spnan>';
                break;
            case '3':
                $status = '<span class="badge badge-info float-right">In Process</spnan>';
                break;    
        }

        return $status;
    }

    public function getPaymentStatusName()
    {
        switch ($this->payment_status) {
            case '1':
                $status = '<span class="badge badge-success float-right config_textbtn">Completed</spnan>';
                break;
            case '0':
                $status = '<span class="badge badge-warning float-right">Pending</spnan>';
                break;
        }

        return $status;
    }
}
