<?php

namespace PAMH;

class PricingCalculator implements PricingCalculatorInterface
{
	/**
	 * PriceHolder object
	 *
	 * @var PriceHolder $priceHolder
	 */
	protected $priceHolder;

	/**
	 * Array containing partial results
	 *
	 * @var array $partialResults
	 */
	protected $partialResults;

	public function __construct(PriceHolder $priceHolder)
	{
		$this->priceHolder = $priceHolder;
		$this->partialResults = [0.0];
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

	    $result = array_sum($this->partialResults);
	    $this->partialResults = [0.0];
        return (float) $result;
    }
}
