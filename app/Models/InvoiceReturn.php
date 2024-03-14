<?php

namespace App\Models;
// use App\Models\Scopes\selectedHotelResource;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Hotel\Entities\Hotel;
use Illuminate\Database\Eloquent\Model;

class InvoiceReturn extends Model
{
    use Sluggable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'return_no', 'reason', 'slug', 'invoice_id', 'transaction_id', 'total_return','total_return_tax','date', 'note', 'status', 'created_by',
    ];

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

    /**
     * Get the invocie for this return.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    // public function hotel()
    // {
    //     return $this->belongsTo(Hotel::class);
    // }

    /**
     * Get the invoice return products.
     */
    public function invoiceReturnProducts()
    {
        return $this->hasMany(InvoiceReturnProduct::class, 'return_id')->orderBy('product_id');
    }

    /**
     * Get the transaction for this return.
     */
    public function returnTransaction()
    {
        return $this->belongsTo(AccountTransaction::class, 'transaction_id');
    }

    /**
     * Get the user who had created this return.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new SelectedHotelResource);
    // }
}
