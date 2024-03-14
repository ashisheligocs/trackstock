<?php

namespace Modules\Restaurant\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OptionalItem extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function prices()
    {
        return $this->hasMany(OptionalItemPrice::class);
    }
}
