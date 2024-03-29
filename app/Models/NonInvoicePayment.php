<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Shops\Entities\Hotel;

class NonInvoicePayment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id', 'slug', 'amount', 'type', 'transaction_id', 'cheque_no', 'receipt_no',  'date', 'note', 'status', 'created_by', 'hotel_id' , 'order_id'
    ];

    /**
     * Get the client
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get the  tansaction for this payment.
     */
    public function paymentTransaction()
    {
        return $this->belongsTo(AccountTransaction::class, 'transaction_id');
    }

    /**
     * Get the user who has created this account
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
