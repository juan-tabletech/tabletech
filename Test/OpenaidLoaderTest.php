<?php

use Tabletech\Classes\OpenaidLoader;

require_once('Tabletech\Classes\OpenaidLoader.php');

class OpenaidLoaderTest extends \PHPUnit_Framework_TestCase
{
	public function testLoader()
	{	
		$loader = new OpenaidLoader;

		//Check the OpenaidLoader country getter method
		$this->assertTrue( $loader->getCountry() === 'SD' );

		//Check the OpenaidLoader country setter method
		$this->assertTrue( $loader->setCountry('ES')->getCountry() === 'ES' );

		$loader->setCountry('SD');

		$apiURL = $loader->getApiUrl();

		//Check the OpenaidLoader API url setter method
		$this->assertTrue( $loader->setApiUrl('new_url')->getApiUrl() === 'new_url' );

		$loader->setApiUrl($apiURL);

		$res = $loader->load(2014, 2015);

		//Check the OpenaidLoader API correct response 
		$this->assertTrue( get_class($res)==='DOMDocument' );


	}
    
}