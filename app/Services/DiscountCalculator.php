<?php

namespace App\Services;

class DiscountCalculator
{
    /**
     * Calculate discount amount.
     *
     * @param float $amount
     * @param float $discountPercentage
     * @return float
     */
    public function calculate(float $amount, float $discountPercentage): float
    {
        return $amount - ($amount * ($discountPercentage / 100));
    }

    public function getShippingFee(float $amount, float $total) : float
    {
        return $total - $amount;
    }
}
