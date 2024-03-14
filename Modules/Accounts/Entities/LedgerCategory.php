<?php

namespace Modules\Accounts\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LedgerCategory extends Model
{
    use HasFactory;
    protected $table = 'ledgers_categories';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'coa_id', 'name', 'system_name', 'position', 'del_status','parant_id', 'expense_category_id'];

    public function ledger(){
        return $this->belongsTo(Ledger::class, 'coa_id');
    }
}
