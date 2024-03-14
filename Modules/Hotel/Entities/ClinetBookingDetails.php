<?php

namespace Modules\Hotel\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClinetBookingDetails extends Model
{
    use HasFactory;
    protected $table = 'client_bookigs_details';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'booking_id', 'customer_id'];

}
