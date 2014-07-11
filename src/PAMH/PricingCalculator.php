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
		$dailyCharge = $this->calculateDailyCharge($start, $stop);
		// ...

		// Just for now
		$this->partialResults[] = min($hourlyCharge, $dailyCharge);
	}

	private function calculateHourlyCharge(Carbon $start, Carbon $stop)
	{
		$diffInHours = $start->diffInHours($stop);

		return $this->priceHolder->getHourly() * $diffInHours;
	}

	private function calculateDailyCharge(Carbon $start, Carbon $stop)
	{
		$diffInDays = $this->calculateDiffInDays($start, $stop);

		return $this->priceHolder->getDaily() * $diffInDays;
	}

	/**
	 * Calculate diff in days.
	 * Respect "5AM rule".
	 *
	 * @param Carbon $start
	 * @param Carbon $stop
	 * @return int
	 */
	private function calculateDiffInDays(Carbon $start, Carbon $stop) {
		$diffInDays = $start->startOfDay()->diffInDays($stop->endOfDay());

		if ($stop->hour < 5)
			$diffInDays--;

		// Make minimum as one day, so for example 1 day can be cheaper than 22 hours
		if ($diffInDays <= 0)
			$diffInDays = 1;

		return $diffInDays;
	}
}
