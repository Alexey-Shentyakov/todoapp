<?php

namespace core;

abstract class Controller
{
	protected $route_params = [];
	
	public function __construct($route_params) {
		$this->route_params = $route_params;
	}
	
	public function __call($name, $args) {
		$method = $name . 'Action';
		
		if (method_exists($this, $method)) {
			$this->before($method);
			
			call_user_func_array([$this, $method], $args);
			
			$this->after();
		}
	}
	
	// before filter - called before an action method
	protected function before($method)
	{
	}
	
	// after filter - called after an action method
	protected function after()
	{
	}
}
