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
	 *
	 * @param Carbon[] $period
	 * @throws \BadMethodCallException
	 */
	private function calculatePeriod($period)
	{
		if (count($period) !== 2) throw new \BadMethodCallException();

		$start = $period[0];
		$stop = $period[1];

		// Get hourly charge
		$hourlyCharge = $this->calculateHourlyCharge($start, $stop);
		$dailyCharge = $this->calculateDailyCharge($start, $stop);
		$weeklyCharge = $this->calculateWeeklyCharge($start, $stop);
		$weeklyPlusDailyCharge = $this->calculateMonthlyPlusDailyCharge($start, $stop);
		$monthlyCharge = $this->calculateMonthlyCharge($start, $stop);

		// Don't count nulls!
		$this->partialResults[] = min(array_filter([$hourlyCharge, $dailyCharge, $weeklyCharge, $weeklyPlusDailyCharge,
			$monthlyCharge]));
	}

	private function calculateHourlyCharge(Carbon $start, Carbon $stop)
	{
		$diffInHours = $start->diffInHours($stop);

		if ($diffInHours > 24)
			return null;

		return $this->priceHolder->getHourly() * $diffInHours;
	}

	private function calculateDailyCharge(Carbon $start, Carbon $stop)
	{
		$diffInDays = $this->calculateDiffInDays($start, $stop);

		return $this->priceHolder->getDaily() * $diffInDays;
	}


	/**
	 * @param Carbon $start
	 * @param Carbon $stop
	 *
	 * @return float
	 */
	private function calculateWeeklyCharge(Carbon $start, Carbon $stop)
	{
		$diffInDays = $start->copy()->startOfDay()->diffInDays($stop->copy()->startOfDay());

		$diffInWeeks = ceil($diffInDays / 7);

		return $diffInWeeks * $this->priceHolder->getWeekly();
	}

	/**
	 * Calculate diff in days.
	 * Respect "5AM rule".
	 *
	 * @param Carbon $start
	 * @param Carbon $stop
	 * @return int
	 */
	private function calculateDiffInDays(Carbon $start, Carbon $stop)
	{
		$diffInDays = $start->startOfDay()->diffInDays($stop->copy()->startOfDay()->addDay());

		if ($stop->hour < 5)
			$diffInDays--;

		// Make minimum as one day, so for example 1 day can be cheaper than 22 hours
		if ($diffInDays <= 0)
			$diffInDays = 1;

		return $diffInDays;
	}

	private function calculateMonthlyCharge(Carbon $start, Carbon $stop)
	{
		$diffInMonths = $start->diffInMonths($stop);

		if ($diffInMonths == 0)
			$diffInMonths = 1;

		return $diffInMonths * $this->priceHolder->getMonthly();
	}

	private function calculateMonthlyPlusDailyCharge(Carbon $start, Carbon $stop)
	{
		$diffInDays = $this->calculateDiffInDays($start, $stop);

		$weeks = floor($diffInDays / 7);
		$restOfDays = $diffInDays % 7;

		return ( $weeks * $this->priceHolder->getWeekly() ) + ( $restOfDays * $this->priceHolder->getDaily() );
	}

}
