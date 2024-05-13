<?php

namespace App\Models;

use App\DB\Core\StringField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentProvider extends Model
{
    use HasFactory;

    public function saveableFields(): array|StringField
    {
        return [
            'paymentmode' => StringField::new()
        ];
    }
}
