<?php

namespace Modules\Restaurant\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FoodCategory extends Model
{
    use HasFactory;
    protected $table = 'foodcategorys';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'category_name', 'image', 'status', 'del_status'];

    public function items()
    {
        return $this->hasMany(Item::class, 'category_id');
    }
}
