<?php

namespace Tests\Unit\discount;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\UnitTestCase;

class DiscountTest extends UnitTestCase
{
    // #[Test]
    #[DataProvider('discountDataProvider')]
    public function test_discount_successful($price, $discount, $expectedDiscountedPrice): void
    {
        $model = app("App\Models\\Percentage");
        $discountPrice = $model->discount($price, $discount);
        
        $this->assertEquals($expectedDiscountedPrice, $discountPrice);
    }

    public static function discountDataProvider()
    {
        return [
            [1000, 10, 900], 
            [2000, 20, 1700], 
        ];
    }
}
