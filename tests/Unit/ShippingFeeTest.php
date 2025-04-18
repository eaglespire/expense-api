<?php

namespace Tests\Unit;

use App\Services\DiscountCalculator;
use PHPUnit\Framework\TestCase;

class ShippingFeeTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_get_shipping_fee(): void
    {
        $calculator = new DiscountCalculator;

        $response = $calculator->getShippingFee(amount: 200,total: 500);

        $this->assertEquals(300,$response);
    }
}
