<?php

namespace PAMH;

use Carbon\Carbon;

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
	    foreach ($periods as $period) {
			$this->calculatePeriod($period);
	    }


	    $result = array_sum($this->partialResults);
	    $this->resetPartialResults();
	    return (float) $result;
    }

	/**
	 * Reset partial results, so next calculation isn't affected by previous one.
	 */
	private function resetPartialResults()
	{
		$this->partialResults = [0.0];
	}

	/**
	 * Calculate price for one period.
 	 */
	private function calculatePeriod($period)
	{
		if (count($period) !== 2) throw new \BadMethodCallException();

		$start = $period[0];
		$stop = $period[1];

		// Get hourly charge
		$hourlyCharge = $this->calculateHourlyCharge($start, $stop);
		// ...

		// Just for now
		$this->partialResults[] = $hourlyCharge;
	}

	private function calculateHourlyCharge(Carbon $start, Carbon $stop)
	{
		$diffInHours = $start->diffInHours($stop);

		return $this->priceHolder->getHourly() * $diffInHours;
	}
}
