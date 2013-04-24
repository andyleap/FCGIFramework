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
		$regex = '#^' . preg_replace_callback('/(\/):([A-Za-z0-9](?:[A-Za-z0-9-_]*[A-Za-z0-9]|))(?:#([^\/#]*)#?)?/', function($matches)
		{
			if(empty($matches[3]))
			{
				return '(?:' . $matches[1] . '(?<' . $matches[2] . '>[^/]*))?';
			}
			else
			{
				$pattern = $matches[3];
				switch($matches[3])
				{
					case 'a-z':
						$pattern = '[a-z]*';
						break;
					case '0-9':
						$pattern = '[0-9]*';
						break;
					case 'a-z0-9':
						$pattern = '[a-z0-9]*';
						break;
					case '0-9a-z':
						$pattern = '[a-z0-9]*';
						break;
				}
				return '(?:' . $matches[1] . '(?<' . $matches[2] . '>' . $pattern . '))';
			}
		}, $route) . '$#';
		$this->routes[] = array('regex' => $regex, 'handler' => $handler);
		//echo $regex;
	}

	function Route($request)
	{
		$matches = array();
		foreach($this->routes as $route)
		{
			if(preg_match($route['regex'], $request->SERVER['PATH_INFO'], $matches))
			{
				$matches = array_merge($route['handler'], array_filter($matches));
				if(isset($matches['controller']) && isset($matches['action']))
				{
					if(isset($this->Controllers[$matches['controller']]) && method_exists($this->Controllers[$matches['controller']], $matches['action']))
					{
						$this->Controllers[$matches['controller']]->ControllerPrep($request, $matches);
						$args = array_map(function($arg) use ($matches)
								{
									if(!empty($matches[$arg]))
									{
										return $matches[$arg];
									}
									return null;
								}, $this->Controllers[$matches['controller']]->methodargs[$matches['action']]);
						return call_user_func_array(array($this->Controllers[$matches['controller']], $matches['action']), $args);
						$this->Controllers[$matches['controller']]->ControllerClean();
					}
				}
			}
		}
	}

}