<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxSlab extends Model
{
    use HasFactory;

    protected $fillable = ['range'];

    public function taxes()
    {
        return $this->belongsToMany(VatRate::class, 'tax_tax_slab', 'vat_rate_id', 'tax_slab_id');
    }

}
