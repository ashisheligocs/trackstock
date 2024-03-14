<?php

namespace Modules\Hotel\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hotelcategory extends Model
{
    use HasFactory;
    protected $table = 'hotelcategories';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'del_status', 'category_name'];

    public function hotel()
    {
        return $this->hasMany(Hotel::class);
    }
}
