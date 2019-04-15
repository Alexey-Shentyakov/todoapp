<?php

namespace core;

class Router
{
	protected $routes = [];
	protected $params = [];

    /**
     * convert the route to a regular expresion and save into the routes array
     */
    public function add($route, $params = []) {
		// escape forward slashes
		$route = preg_replace('/\//', '\\/', $route);
//var_dump($route);exit;

		// convert variables
        // {controller}/{action} -> string(49) "/^(?P<controller>[a-z-]+)\/(?P<action>[a-z-]+)$/i"
		$route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
//var_dump($route);exit;

		// convert variables with custom regular expressions e.g. {id:\d+}
        // {controller}/{id:\d+}/{action} -> string(62) "/^(?P<controller>[a-z-]+)\/(?P<id>\d+)\/(?P<action>[a-z-]+)$/i"
		$route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
//var_dump($route);exit;
		
		// add start and end delimiters and case insensitive
		$route = '/^' . $route . '$/i';
//var_dump($route);exit;

		$this->routes[$route] = $params;
	}
	
	public function getRoutes() {
		return $this->routes;
	}
	
	public function getParams() {
		return $this->params;
	}
	
	public function match($url) {
		foreach ($this->routes as $route => $params) {
			if (preg_match($route, $url, $matches)) {
				foreach ($matches as $key => $match) {
					if (is_string($key)) {
						$params[$key] = $match;
					}
				}
				
				$this->params = $params;
				return true;
			}
		}
		
		return false;
	}
	
	public function dispatch($url) {
		if ($this->match($url)) {
//var_dump($this->params);exit;
			$controller = $this->params['controller'];
			$controller = $this->convertToStudlyCaps($controller) . 'Controller';
			$controller = $this->getNamespace() . $controller;
//var_dump($controller);exit;
			if (class_exists($controller)) {
				$controller_object = new $controller($this->params);
				
				$action = $this->params['action'];
				$action = $this->convertToCamelCase($action);
//var_dump($action);exit;
				if (is_callable([$controller_object, $action])) {
					$controller_object->$action($this->params);
				}
				else {
					echo "Method $action (in controller $controller) not found";
				}
			}
			else {
				echo "Controller class $controller not found";
			}
		} else {
			echo "No route matched.";
		}
	}
	
	private function convertToStudlyCaps($string) {
		return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
	}
	
	private function convertToCamelCase($string) {
		return lcfirst($this->convertToStudlyCaps($string));
	}
	
	protected function getNamespace() {
		$namespace = 'application\\controllers\\';
		
		if (array_key_exists('namespace', $this->params)) {
			$namespace .= $this->params['namespace'] . '\\';
		}
		
		return $namespace;
	}
}
