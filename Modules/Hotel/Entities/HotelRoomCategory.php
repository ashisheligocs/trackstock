<?php

namespace Modules\Hotel\Entities;

use App\Models\Scopes\selectedHotelResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\VatRate;

class HotelRoomCategory extends Model
{
    use HasFactory;
    protected $table = 'hotel_room_category';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'hotel_id', 'room_category', 'rate', 'extra_bed_capacity', 'extra_bed_rate', 'extra_person_capacity',
    'per_person','no_of_child','no_of_infant','no_of_person'];

    public function roomCategory()
    {
        return $this->belongsTo(Roomcategory::class, 'room_category');
    }

    public function Hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function rooms(){
        return $this->hasMany(Room::class, 'hotel_room_category_id');
    }

    public function taxRate()
    {
        return $this->hasMany(RoomCategoryTax::class, 'hotel_room_category_id');
    }

    public function taxName()
    {
        return $this->belongsToMany(VatRate::class, 'room_category_tax','hotel_room_category_id','tax_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SelectedHotelResource);
    }
}
