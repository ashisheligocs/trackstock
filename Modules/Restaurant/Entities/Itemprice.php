<?php

namespace Modules\Restaurant\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Itemprice extends Model
{
    use HasFactory;
    protected $table = 'item_prices';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'item_id', 'varient_id', 'price'];
    
    public function varient()
    {
        return $this->belongsTo(Varient::class, 'varient_id');
    }

    public function items()
    {
        return $this->belongsTo(Itemprice::class, 'item_id');
    }
}
