<?php

namespace Tabletech\Classes;

class BaseController
{   

	/**
	 * Execute an action controller method building the method name
	 * @param  string $action action method name
	 * @param  array $params List of params
	 * @return Method result
	 */
	public function execAction($action, $params)
	{	
		if(method_exists($this,$MethodName=$action.'Action')){
			return $this->$MethodName($params);
		}else{
	        $trace = debug_backtrace();
	        trigger_error(
	            'Unknow ation: ' . $MethodName .
	            ' in ' . $trace[0]['file'] .
	            ' line ' . $trace[0]['line'],
	            E_USER_NOTICE);
	        return null;
		}
	}	

}
