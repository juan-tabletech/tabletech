<?php

namespace Tabletech\Controllers;

use Tabletech\Classes\BaseController;
use Tabletech\Classes\OpenaidLoader;
use Tabletech\Classes\CurrencyToUSD;
use resources\Constants;

class showDataController extends BaseController
{   
	/**
	 * Call to get the specified country data from the IATI datastore
	 * @param  array $params list of request params
	 */
	public function processAction($params)
	{
		//If there is no country code
		if(!array_key_exists('country', $params)){
			raiseError("You must specify a country code. For instance: 'SD'.", Constants::HTTP_BAD_REQUEST);
		}

		//Get the country code
		$country = strtoupper($params['country']);

		//Prepare an instance of OpenaidLoader
		$loader = new OpenaidLoader($country);

		//Get a XML with data from last year to five years back
		$lastYear = intval(date('Y', strtotime('-1 year')));
		$lastYearFiveBack = $lastYear - 4;

		//Get the XML data from the API 
		$xml = $loader->load($lastYearFiveBack, $lastYear);	

		$xpath = new \DOMXPath($xml);

		//Final data array
		$data = array();

		//Instance of currency to USD converter
		$cToUSD = new CurrencyToUSD();
		
		//Get all the activities
		//$activities = $xpath->query("//iati-activity[@humanitarian='1']");
		$activities = $xpath->query("//iati-activity");

		//Iterate each activity
		foreach ($activities as $activity) {		
			$transactions = $xpath->query(
				"transaction[transaction-type[@code='3']
					and number(translate(transaction-date/@iso-date,'-','')) >= ".$lastYearFiveBack."0101
					and number(translate(transaction-date/@iso-date,'-','')) <= ".$lastYear."0101
				]
				", $activity);

			$defaultCurrency = null;

			/**
			 * <iati-activity iati-extra:version="2.01" last-updated-datetime="2016-06-30T00:00:00" xml:lang="en" default-currency="USD" hierarchy="1" linked-data-uri="">
			 */
			if($activity->hasAttribute('default-currency')){
				$defaultCurrency = $activity->getAttribute('default-currency');
			}
			
			//Iterate each transaction
			foreach ($transactions as $transaction) {
				/**
				 * <value currency="USD" value-date="2015-01-01">2040</value>
				 */
				$valueNode = $xpath->query("value", $transaction)->item(0);
				$value = $valueNode->nodeValue;

				/**
				 * <transaction-date iso-date="2015-12-31"/>
				 */
				$date = $xpath->query("transaction-date/@iso-date", $transaction)->item(0);
				$year = substr($date->nodeValue, 0, 4);

				/**
				 * <provider-org ref="" provider-activity-id="">
				 *  	<narrative xml:lang="en">Global - Thematic Humanitarian Resp</narrative>
				 * </provider-org>
				 */
				$providerOrgNode = $xpath->query("provider-org/narrative", $transaction)->item(0);

				if($providerOrgNode){
					$providerOrg = $providerOrgNode->nodeValue;
				}else{
					$providerOrg = "Others";
				}
				

				//If there is no activity default currency, get the transaction currency
				if(!$defaultCurrency){

					$a = $valueNode->getAttribute('currency');

					$defaultCurrency = $valueNode->getAttribute('currency');

				}

				//Add the value to the year and its currency converted to USD.
				if( array_key_exists($year, $data) ){

					if( array_key_exists($providerOrg, $data[$year]) ){
						$data[$year][$providerOrg] += $cToUSD->convert($value, $defaultCurrency);
					}else{
						$data[$year][$providerOrg] = $cToUSD->convert($value, $defaultCurrency);	
					}
					
				}else{
					$data[$year][$providerOrg] = $cToUSD->convert($value, $defaultCurrency);
				}			

			}//foreach		

		}//foreach	

		//Order the providers figures in reverse order
		foreach ($data as $key => &$value) {
			arsort($value);
		}	

		//Order the years in reverse order
		krsort($data);		

		//Print the result as JSON (no view)
		echo(json_encode($data));

	}
}
