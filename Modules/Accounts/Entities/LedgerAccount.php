<?php

namespace Modules\Accounts\Entities;

use App\Models\Scopes\selectedHotelResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \App\Models\Account;
use \App\Models\AccountTransaction;
use Modules\Shops\Entities\Shop;
class LedgerAccount extends Model
{
    use HasFactory;
    protected $table = 'ledgers_accounts';
    protected $primaryKey = 'id';
    protected $fillable = ['ledger_type', 'ledger_group', 'ledger_name', 'system_name', 'code', 'is_bank', 'show_in_day_book', 'del_status', 'hotel_id', 'expense_sub_category_id'];
    protected $appends = ['disabled', 'editable'];

    public function ledger()
    {
        return $this->belongsTo(Ledger::class, 'ledger_type');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function ledgerCategory()
    {
        return $this->belongsTo(LedgerCategory::class, 'ledger_group');
    }

    public function getAccoutnbalance()
    {
        return $this->hasOne(Account::class, 'account_id');
    }

    public function balanceTransactions()
    {
        return $this->hasMany(AccountTransaction::class, 'account_id');
    }

    public function getDisabledAttribute()
    {
        return in_array($this->code, ["CASH-0001", "SALARY", "ACCOUNT-PAYABLE", "ACCOUNT-RECEIVABLE", "GST-INPUT", "ROUND-OFF-EXPENSE",
                                      "GST-OUTPUT", "OPERATING_REVENUE", "EQUITY", "INVENTORY", "CHARGES", "ROUND-OFF-REVENUE"]);
    }

    public function getEditableAttribute()
    {
        return !$this->getAccoutnbalance()->whereHas('balanceTransactions')->exists();
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SelectedHotelResource);
    }
}
