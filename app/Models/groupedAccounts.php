<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class groupedAccounts extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function account1()
    {
        return $this->belongsTo(accounts::class, 'account1_id');
    }

    public function account2()
    {
        return $this->belongsTo(accounts::class, 'account2_id');
    }
}
