<?php

namespace Tests\Unit\discount;

use Illuminate\Support\Number;
use Tests\TestCase;

class DiscountTest extends TestCase
{
    public function test_discount_successful(float $price, $percent): void
    {
        $discount_price = Number::format($price - ($price * $percent), 2);
        $this->assertEquals(900, $discount_price);
    }
}
