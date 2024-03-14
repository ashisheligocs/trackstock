<?php

namespace Modules\Accounts\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ledger extends Model
{
    use HasFactory;
    protected $table = 'ledgers';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'name', 'position'];
}
