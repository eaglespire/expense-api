<?php

namespace Tests\Unit;

use App\Services\DiscountCalculator;
use PHPUnit\Framework\TestCase;

class DiscountCalculatorTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_calculate_discount(): void
    {
        $calculator = new DiscountCalculator;

        $discount = $calculator->calculate(amount: 500,discountPercentage: 10);

        $this->assertEquals(450,$discount);
    }
}
