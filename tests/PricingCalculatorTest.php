<?php

use Illuminate\Container\Container;

class PricingCalculatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Pricing calculator instance.
     *
     * @var PAMH\PricingCalculator
     */
    private $calculator;

	/**
	 * Instance of PriceHolder.
	 * Will be used to get prices so we can make assertions
	 *
	 * @var PAMH\PriceHolder
	 */
	private $priceHolder;

	/**
     * Instantiate the PricingCalculator class using the Laravel IoC container.
     *
     * This container will allow for automatic dependency injection based upon
     * constructor type hinting. See the Laravel documentation at
     *
     *  http://laravel.com/docs/ioc
     *
     * for more information.
     */
    public function setUp()
    {
        // Create a new Laravel container instance.
        $container = new Container;

        // Resolve the pricing calculator (and any type hinted dependencies)
        // and set to class attribute.
        $this->calculator = $container->make('PAMH\\PricingCalculator');
	    $this->priceHolder = $container->make('PAMH\\PriceHolder');
    }

    /**
     * Ensure that the pricing calculator can be resolved from the
     * Laravel IoC container.
     */
    public function testPricingCalculatorCanBeResolved()
    {
        $container = new Container;
        $calculator = $container->make('PAMH\\PricingCalculator');
        $this->assertTrue($calculator instanceof \PAMH\PricingCalculatorInterface);
    }

    /**
     * Ensure that an empty array of time periods returns zero.
     */
    public function testEmptyArrayOfPeriodsReturnsZero()
    {
        $result = $this->calculator->calculate([]);
        $this->assertEquals(0, $result);
    }

	/**
	 * One hour will be always cheaper than daily charge, so it's safe to test one hour as just one hour price
	 */
	public function testCalculateOneHour()
	{
		$hourlyPrice = $this->priceHolder->getHourly();

		$now = \Carbon\Carbon::now();
		$oneHourLater = \Carbon\Carbon::now()->addHour();
		$result = $this->calculator->calculate([ [$now, $oneHourLater] ]);

		$this->assertEquals($hourlyPrice, $result);
	}

	public function testCalculateTwoHours()
	{
		$hourlyPrice = $this->priceHolder->getHourly();
		$dailyPrice = $this->priceHolder->getDaily();

		// If 2 hours are more expensive than dailyPrice - take daily
		$correctCharge = ($hourlyPrice * 2) > $dailyPrice ? $dailyPrice : ($hourlyPrice *2);

		$now = \Carbon\Carbon::now();
		$twoHoursLater = \Carbon\Carbon::now()->addHours(2);

		$this->assertEquals($correctCharge, $this->calculator->calculate([ [$now, $twoHoursLater] ]));
	}

	public function testCalculateThreeHours()
	{
		$hourlyPrice = $this->priceHolder->getHourly();
		$dailyPrice = $this->priceHolder->getDaily();

		// If 2 hours are more expensive than dailyPrice - take daily
		$correctCharge = ($hourlyPrice * 3) > $dailyPrice ? $dailyPrice : ($hourlyPrice * 3);

		$now = \Carbon\Carbon::now();
		$threeHoursLater = \Carbon\Carbon::now()->addHours(3);

		$this->assertEquals($correctCharge, $this->calculator->calculate([ [$now, $threeHoursLater] ]));
	}

	public function testDontTakeBefore5AmIntoConsideration()
	{
		$start = \Carbon\Carbon::create(2014, 8, 1, 6, 0, 0);
		$stop = \Carbon\Carbon::create(2014, 8, 4, 4, 0, 0);

		// It is 3 days because it finish before 5AM
		// 3 days, counting daily is a 15 pounds, less than weekly, so it should be 15.0

		$dailyCharge = $this->priceHolder->getDaily() * 3;
		$weeklyCharge = $this->priceHolder->getWeekly();

		$correctCharge = $dailyCharge > $weeklyCharge ? $weeklyCharge : $dailyCharge;

		$this->assertEquals($correctCharge, $this->calculator->calculate([ [$start, $stop] ]));
	}

	public function testOnlyWeeklyIsCheaperThanDaily()
	{
		// It is 6 days
		// It would be 6*10 = 60;
		// Weekly it will be 20;

		$start = \Carbon\Carbon::create(2014, 1, 1, 1, 0, 0);
		$stop = \Carbon\Carbon::create(2014, 1, 6, 6, 0, 0);

		$dailyCharge = $this->priceHolder->getDaily() * 6;
		$weeklyCharge = $this->priceHolder->getWeekly();

		$correctCharge = $dailyCharge > $weeklyCharge ? $weeklyCharge : $dailyCharge;

		$this->assertEquals($correctCharge, $this->calculator->calculate([ [$start, $stop] ]));
	}
}
