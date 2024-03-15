<?php

namespace Modules\Shops\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FacilityName extends Model
{
    use HasFactory;
    protected $table = 'room_facilitytdatas';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'del_status', 'facilityId'];

  
    public function room_facilitytdatas()
    {
        return $this->belongsTo(room_facilitytdatas::class, 'room_facilitytdatas');
    }
}
