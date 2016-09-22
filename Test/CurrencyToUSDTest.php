<?php

use Tabletech\Classes\CurrencyToUSD;

require_once('Tabletech\Classes\CurrencyToUSD.php');

class CurrencyToUSDTest extends \PHPUnit_Framework_TestCase
{
	public function testConvert()
	{	
		$conversor = new CurrencyToUSD;

		//Check a valid conersion
		$this->assertTrue( $conversor->convert(1, 'USD') === 1 );

		//Check an invalid conversion
		$this->assertTrue( $conversor->convert(1, 'EUR') !== 1 );

		//Check an unexising currency code
		$this->assertTrue( is_null($conversor->convert(1, 'GALLIFANTE')) );
	}
    
}