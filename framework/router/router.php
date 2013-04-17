<?php

class Router implements CoreInit
{
	private $routes = array();
	private $framework;
	private $Controllers;

	function __construct($framework)
	{
		$this->framework = $framework;
	}

	public function Init()
	{
		$this->Controllers = $this->framework->ControllerCache;
	}

	function AddRoute($route, $handler)
	{
		$regex = '#' . preg_replace('/:([A-Za-z0-9](?:[A-Za-z0-9-_]*[A-Za-z0-9]|))/', '(?<$1>[^/]*)', $route) . '#';
		$this->routes[] = array('regex' => $regex, 'handler' => $handler);
	}

	function Route($request)
	{
		$matches = array();
		foreach($this->routes as $route)
		{
			if(preg_match($route['regex'], $request->SERVER['PATH_INFO'], $matches))
			{
				$matches = array_merge($route['handler'], $matches);
				if(isset($matches['controller']) && isset($matches['action']))
				{
					if(isset($this->Controllers[$matches['controller']]) && method_exists($this->Controllers[$matches['controller']], $matches['action']))
					{
						$this->Controllers[$matches['controller']]->ControllerPrep($request, $matches);
						$args = array_map(function($arg) use ($matches)
								{
									return $matches[$arg];
								}, $this->Controllers[$matches['controller']]->methodargs[$matches['action']]);
						return call_user_func_array(array($this->Controllers[$matches['controller']], $matches['action']), $args);
						$this->Controllers[$matches['controller']]->ControllerClean();
					}
				}
			}
		}
	}

}