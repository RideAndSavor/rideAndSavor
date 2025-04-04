<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WavepayPaymentAccount extends Model
{
    use HasFactory;

    protected $table = 'wavepay_payment_accounts';

    protected $fillable = [
        'shop_id',
        'wavepay_no'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
