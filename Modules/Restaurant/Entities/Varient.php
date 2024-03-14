<?php

namespace Modules\Restaurant\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Varient extends Model
{
    use HasFactory;
    protected $table = 'varients';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'varient_name', 'del_status'];

}
