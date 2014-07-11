<?php

namespace PAMH;

/**
 * Class PriceHolder
 * @package PAMH
 *
 * Just for simplification
 * For real, prices should probably not be as const
 * It should be in some database or whatever
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
		return $this->hour;
	}

	/**
	 * @return float
	 */
	public function getDaily()
	{
		return $this->day;
	}

	/**
	 * @return float
	 */
	public function getWeekly()
	{
		return $this->week;
	}

	/**
	 * @return float
	 */
	public function getMonthly()
	{
		return $this->month;
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