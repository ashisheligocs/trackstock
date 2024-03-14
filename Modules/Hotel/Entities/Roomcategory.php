<?php

namespace Modules\Hotel\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Roomcategory extends Model
{
    use HasFactory;
    protected $table = 'room_categories';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'del_status', 'room_category_name'];
}
