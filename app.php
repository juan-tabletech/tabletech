<?php

/*********************************************
  _____     _     _      _            _     
 |_   _|_ _| |__ | | ___| |_ ___  ___| |__  
   | |/ _` | '_ \| |/ _ \ __/ _ \/ __| '_ \ 
   | | (_| | |_) | |  __/ ||  __/ (__| | | |
   |_|\__,_|_.__/|_|\___|\__\___|\___|_| |_|

*********************************************/

	use Tabletech\Classes\Cache;
	use resources\Constants;
	
	// 
	if (!function_exists('http_response_code'))
	{
		/**
		 * Compatibility method http_response_code for For 4.3.0 <= PHP <= 5.4.0
		 * @param  integer $newcode HTTP Code
		 * @return integer response HTTP Code
		 */
		function http_response_code($newcode = NULL)
		{
			static $code = 200;
			if($newcode !== NULL)
			{
				header('X-PHP-Response-Code: '.$newcode, true, $newcode);
				if(!headers_sent())
					$code = $newcode;
			}
			return $code;
	    }
	}

	/**
	 * Custom error handle method
	 * @param  integer $errno  level of the error raised
	 * @param  string $errstr error message
	 */
	function raiseError($errorMessage, $responseCode){
	    global $errors;

		http_response_code($responseCode);

	    die(json_encode(array(
	    	"error" => $errorMessage
	    )));
	}

	/**
	 * Custom error handle method
	 * @param  integer $errno  level of the error raised
	 * @param  string $errstr error message
	 */
	function errorHandler($errno,$errstr){
	    global $errors;

	    raiseError("We are working to solve an internal issue in our service. Please, try later.", Constants::HTTP_INTERNAL_SERVER_ERROR);
	}

	//Set default timezone
	date_default_timezone_set("Europe/Madrid");

	//Set error reporting to off
	error_reporting(0);

	//Set an error handle
	set_error_handler('errorHandler',E_ALL);

	//Prepare the autoload function
	spl_autoload_register(function ($class) {
	    require str_replace("\\", DIRECTORY_SEPARATOR, $class) . '.php';
	});

	//Prepare an instance of Cache
	$cache = new Cache();

	//Start caching
	$cache->start(); 

	//Get the controller name
	$controller = $_GET['controller'];

	//Get the action to call
	$action = $_GET['action'];

	//Prepare the controller classname
	$controllerClassName = 'Tabletech\Controllers\\'.$controller.'Controller';

	//Instance the controller
	$controller = new $controllerClassName();

	//Call the wanted action method
	$controller->execAction($action, $_GET);

	//Stop caching
	$cache->end(); 