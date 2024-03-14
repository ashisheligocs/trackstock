<?php

namespace Modules\Hotel\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bed extends Model
{
    use HasFactory;
    protected $table = 'beds';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'del_status', 'bedtypetitle'];
    
}
