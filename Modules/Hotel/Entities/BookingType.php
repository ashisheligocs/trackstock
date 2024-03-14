<?php

namespace Modules\Hotel\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingType extends Model
{
    use HasFactory;
    protected $table = 'booking_type';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'del_status', 'bookingtypetitle'];
}
