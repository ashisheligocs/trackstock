<?php

namespace Modules\Hotel\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingAmendStay extends Model
{
    use HasFactory;
    protected $table = 'booking_amend_stay';
    protected $primaryKey = 'id';
    protected $fillable = [
        'hotel_id',
        'booking_id',
        'notes',
        'modified_net_total'
    ];
    

}
