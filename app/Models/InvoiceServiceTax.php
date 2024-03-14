<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceServiceTax extends Model
{
    use HasFactory;


    protected $table = 'invoice_services_tax';
    protected $fillable = [
        'invoice_id', 'tax_id', 'name', 'rate','amount','code'
    ];

    /**
     * Get the invoice for this product.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
