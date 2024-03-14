<?php

namespace Modules\Hotel\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Floor extends Model
{
    use HasFactory;
    protected $table = 'floors';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'del_status', 'floorsName'];
}
