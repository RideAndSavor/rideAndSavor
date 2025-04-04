<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpayPaymentAccount extends Model
{
    use HasFactory;

    protected $fillable = ['shop_id', 'kpay_no'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
