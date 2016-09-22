<?php

namespace Tabletech\Classes;

class OpenaidLoader
{   
	private $apiUrl;
    private $country;

    /**
     * Class constructor
     * @param string $country default country code
     */
    public function __construct($country = 'SD')
    {
        $this->country = $country;
        $this->apiUrl = 'http://datastore.iatistandard.org/api/1/access/activity.xml';
        $this->data = null;
    }

    /**
     * Load the IATI activities with date between two years.
     * @param  string $fromYear Starting filter year
     * @param  string $toYear   Ending filter year
     * @return string           XML result
     */
    private function loadAPIData($fromYear, $toYear)
    {
    	set_time_limit(0);

		$ch = curl_init();

		$url = $this->apiUrl.'?recipient-country='.$this->country.'&end-date__lt='.$toYear.'-01-01&end-date__gt='.$fromYear.'-01-01&participating-org.role=1&stream=True';
		
		$options = array( 
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_RETURNTRANSFER => true,			
			CURLOPT_URL => $url
		);

		curl_setopt_array($ch, $options);
		
		$result = curl_exec($ch);

		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);

		if ($http_status != 404){
			return $result;
		}else{
			return false;
		}
    }

    /**
     * Class country setter
     * @param string $country Country code
     */
	public function setCountry($country)    
	{
		$this->country = $country;

		return $this;
	}

    /**
     * Class country getter
     * @return string  Country code
     */
	public function getCountry()    
	{
		return $this->country;
	}

    /**
     * Class api url setter
     * @param string $url New API url
     */
	public function setApiUrl($url)    
	{
		$this->apiUrl = $url;

		return $this;
	}

    /**
     * Class api url getter
     * @return string  API url
     */
	public function getApiUrl()    
	{
		return $this->apiUrl;
	}

    /**
     * Call to load the IATI data for the activities with date between two years.
     * @param  string $fromYear Starting filter year
     * @param  string $toYear   Ending filter year
	 * @return DOMDocument      XML Result
	 */
	public function load($fromYear, $toYear)
	{		

		$time = time();

		$data = $this->loadAPIData($fromYear, $toYear);
		
		if(!$data) return false;

		$doc = new \DOMDocument;
		$doc->preserveWhiteSpace = false;

		$doc->loadXML($data);

		return $doc;
	}

}
