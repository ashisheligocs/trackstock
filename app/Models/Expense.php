<?php

namespace App\Models;
use App\Models\Scopes\selectedHotelResource;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Shops\Entities\Hotel;

class Expense extends Model
{
    use Sluggable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reason', 'slug', 'sub_cat_id', 'transaction_id', 'date', 'created_by', 'note', 'image_path', 'status', 'hotel_id', 'expense_transaction_id'
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'reason',
            ],
        ];
    }
    /**
     * Get the asset type that owns the asset.
     */
    public function expSubCategory()
    {
        return $this->belongsTo(ExpenseSubCategory::class, 'sub_cat_id');
    }

    /**
     * Get the tansaction for this expense.
     */
    public function expTransaction()
    {
        return $this->belongsTo(AccountTransaction::class, 'transaction_id');
    }

    public function expLiableTransaction()
    {
        return $this->belongsTo(AccountTransaction::class, 'expense_transaction_id');
    }

    /**
     * Get the user who had created this expense.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SelectedHotelResource);
    }
}
