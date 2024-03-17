<?php

namespace Modules\Shops\Entities;

use App\Models\GeneralSetting;
use App\Models\Scopes\SelectedHotel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Shop extends Model
{
    use HasFactory;
    protected $table = 'shops';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'shop_name', 'shop_address','shop_phone', 'shop_phone1',
                           'shop_email', 'shop_prefix', 'shop_Status', 'contact_phone',
                            'contact_name', 'del_status', 'images','state','city','image_path'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_shop');
    }


    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SelectedHotel);
    }
}
