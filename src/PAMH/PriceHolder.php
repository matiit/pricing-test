<?php

namespace PAMH;

/**
 * Class PriceHolder
 * @package PAMH
 *
 * Just for simplification
 * For real, prices should probably not be as const
 * It should be in some database or whatever
 *
 * I know that it could implement some interface, but for this i think it would be overkill, of course I may be wrong.
 */
class PriceHolder
{
	private $hour = 2.0;
	private $day = 5.0;
	private $week = 20.0;
	private $month = 70.0;

	private $visible = ['hour', 'day', 'week', 'month'];

	/**
	 * @return float
	 */
	public function getHourly()
	{
		return (float) $this->hour;
	}

	/**
	 * @return float
	 */
	public function getDaily()
	{
		return (float) $this->day;
	}

	/**
	 * @return float
	 */
	public function getWeekly()
	{
		return (float) $this->week;
	}

	/**
	 * @return float
	 */
	public function getMonthly()
	{
		return (float) $this->month;
	}

	/**
	 * @param string $name
	 * @return float
	 * @throws \BadMethodCallException
	 */
	public function get($name)
	{
		if (!in_array($name, $this->visible))
			throw new \BadMethodCallException();

		return (float) $this->$name;
	}
} 