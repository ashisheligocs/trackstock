<?php

namespace Modules\Restaurant\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RestroInvoice extends Model
{
    protected $table = 'restro_invoice';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'order_id', 'invoice_id', 'order_amount', 'total_amount', 'discount', 'tax'];
}
