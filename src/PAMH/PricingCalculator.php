<?php

namespace PAMH;

class PricingCalculator implements PricingCalculatorInterface
{
	/**
	 * PriceHolder object
	 *
	 * @var PriceHolder
	 */
	protected $priceHolder;

	public function __construct(PriceHolder $priceHolder)
	{
		$this->priceHolder = $priceHolder;
	}

    /**
     * Calculate a price based upon an array of start and
     * end date pairs.
     *
     * @param  array  $periods
     * @return float
     */
    public function calculate(array $periods)
    {
        return 0;
    }
}
