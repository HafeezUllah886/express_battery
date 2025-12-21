<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class claim extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(accounts::class, 'customerID');
    }

    public function vendor()
    {
        return $this->belongsTo(accounts::class, 'vendorID');
    }

    public function product()
    {
        return $this->belongsTo(products::class, 'productID');
    }
}
