<?php

use Illuminate\Container\Container;

class PriceHolderTest extends PHPUnit_Framework_TestCase
{
	/**
	 * PriceHolder instance.
	 *
	 * @var PAMH\PriceHolder
	 */
	private $priceHolder;

	/**
	 * Instantiate the PriceHolder class using the Laravel IoC container.
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
		$this->priceHolder = $container->make('PAMH\\PriceHolder');
	}

	/**
	 * Ensure that the PriceHolder can be resolved from the
	 * Laravel IoC container.
	 */
	public function testPriceHolderCanBeResolved()
	{
		$container = new Container;
		$priceHolder = $container->make('PAMH\\PriceHolder');
		$this->assertTrue($priceHolder instanceof \PAMH\PriceHolder);
	}

}
