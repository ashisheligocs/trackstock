<?php

namespace Modules\Restaurant\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OptionalItemPrice extends Model
{
    use HasFactory;

    protected $fillable = ['restaurant_id', 'optional_item_id', 'item_id', 'price', 'active'];

    public function optionalItem()
    {
        return $this->belongsTo(OptionalItem::class);
    }
}
