<?php

namespace Modules\Accounts\Entities;

use App\Models\Scopes\selectedHotelResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Shops\Entities\Shop;
use \App\Models\AccountTransaction;
use Modules\Accounts\Entities\LedgerAccount;

class JournalEntry extends Model
{
    use HasFactory;
    protected $table = 'journal_entry';
    protected $primaryKey = 'id';
    protected $fillable = ['id','hotel_id','date', 'note', 'amount'];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function balanceTransactions()
    {
        return $this->hasMany(AccountTransaction::class, 'journal_entry_id');
    }
    
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SelectedHotelResource);
    }
}
