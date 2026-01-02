<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class claim_amount extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(accounts::class, 'customer_id');
    }

    public function product()
    {
        return $this->belongsTo(products::class, 'product_id');
    }

    public function vendor()
    {
        return $this->belongsTo(accounts::class, 'vendor_id');
    }
}
