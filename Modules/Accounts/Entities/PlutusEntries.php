<?php

namespace Modules\Accounts\Entities;

use App\Models\AccountTransaction;
use App\Models\Scopes\SelectedHotel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Scopes\selectedHotelResource;
use Modules\Hotel\Entities\Hotel;

class PlutusEntries extends Model
{
    use HasFactory;

    protected $table = 'plutus_entries';
    protected $primaryKey = 'id';
    protected $fillable = ['id','hotel_id','date','note','amount'];

    public function hotel(){
        return $this->belongsTo(Hotel::class);
    }

    public function balanceTransactions(){
        return $this->hasMany(AccountTransaction::class,'plutus_entries_id');
    }

    protected static function boot(){
        parent::boot();
        static::addGlobalScope(new SelectedHotelResource);
    }
}
