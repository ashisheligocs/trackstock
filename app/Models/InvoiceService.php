<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceService extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'other_invoice_services';
    protected $fillable = [
        'service_id','service_name','invoice_id', 'quantity', 'price', 'myTax',
    ];

    /**
     * Get the invoice for this product.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    
    
}
