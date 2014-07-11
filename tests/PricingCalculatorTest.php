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
}
