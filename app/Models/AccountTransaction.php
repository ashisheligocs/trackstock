<?php

namespace App\Models;

use App\Models\Scopes\selectedHotelResource;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounts\Entities\LedgerAccount;
use Modules\Shops\Entities\Booking;
use Modules\Shops\Entities\Hotel;
use App\Models\Client;
use Modules\Shops\Entities\Shop;

class AccountTransaction extends Model
{
    use Sluggable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id', 'reason', 'amount', 'order_id','type', 'transaction_date', 'cheque_no', 'receipt_no','invoice_id', 'created_by', 'note', 'status', 'booking_id',
        'purchase_id', 'shop_id','journal_entry_id','customer_id','plutus_entries_id','bank_charges','payroll_id',
    ];

//    protected $appends = ['account_type'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'reason',
            ],
        ];
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
    *Get Customer Info 24-11-2023 (Abhi)
    */
    
    public function customer()
    {
        return $this->belongsTo(Client::class, 'customer_id');
    }

    

    /**
     * Get the total credits
     */
    public function totalCredits()
    {
        return $this->where('status', 1)->where('type', 1)->sum('amount');
    }

    /**
     * Get the all debits
     */
    public function totalDebits()
    {
        return $this->where('status', 1)->where('type', 0)->sum('amount');
    }

    /**
     * Get the available balance
     */
    public function availableBalance()
    {
        return $this->totalCredits() - $this->totalDebits();
    }

    /**
     * Get the account that owns this transaction
     */
    public function cashbookAccount()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

     /**
     * Get the account that owns this transaction
     */
    public function ledgerAccount()
    {
        return $this->belongsTo(LedgerAccount::class, 'account_id');
    }

    /**
     * Get the user who has created this transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getAccountTypeAttribute()
    {
        return @$this->cashbookAccount->ledgerAccount->ledger->id == 1 ? ($this->type == 1 ? 0 : 1) : $this->type;
    }

    public function getIsBankTransactionAttribute()
    {
        return @$this->cashbookAccount->ledgerAccount->is_bank ? 1 : 0;
    }

    public function getAssetAttribute()
    {
        return @$this->cashbookAccount->ledgerAccount->ledger->id == 1
                || $this->isInputGstTransaction() ? 1 : 0;
    }

    public function isInputGstTransaction()
    {
        return $this->cashbookAccount->ledgerAccount->code === 'GST-INPUT';
    }

    public function getExpenseAttribute()
    {
        return @$this->cashbookAccount->ledgerAccount->ledger->id == 5 ? 1 : 0;
    }

    public function scopeShop($query, $shopId){
        return $query->where('shop_id', $shopId)->orWhereNull('shop_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SelectedHotelResource);
    }
}
