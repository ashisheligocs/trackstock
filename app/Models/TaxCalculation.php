<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxCalculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'vat_rate_id',
        'input',
        'output',
        'type',
        'reference',
    ];

    public function vatRate()
    {
        return $this->belongsTo(VatRate::class, 'vat_rate_id');
    }
}
