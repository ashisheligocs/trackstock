<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_code', 'client_id', 'discount_type', 'discount_value', 'start_time', 'end_time', 'expiry','status','coupon_use'
    ];

    public function couponHotel(){
        return $this->hasMany(CouponsHotel::class,'coupon_id');
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }
}
