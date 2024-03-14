<?php

namespace Modules\Hotel\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingStatus extends Model
{
    use HasFactory;
    protected $table = 'booking_status';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'name'];
    
}
