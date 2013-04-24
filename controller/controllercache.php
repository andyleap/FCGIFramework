<?php

class ControllerCache implements CoreInit, ArrayAccess
{
	private $Controllers = array();
	private $framework;
	
	function __construct($framework)
	{
		$this->framework = $framework;
		$controllerDir = $this->framework->Options['MainDir'] . DS . $this->framework->Options['ControllerDir'];
		$dirs = array($controllerDir);
		while(($dir = array_shift($dirs)) !== NULL)
		{
			if(($dirHandle = @opendir($dir)) !== false)
			{
				while(($file = readdir($dirHandle)) !== false)
				{
					if($file == '.' || $file == '..')
					{
						continue;
					}
					if(is_dir($dir . DS . $file))
					{
						array_push($dirs, $dir . DS . $file);
					}
					else
					{
						if(preg_match('/^.+php$/', $file))
						{
							include_once $dir . DS . $file;
						}
					}
				}
				closedir($dirHandle);
			}
		}
	}
	
	public function Init()
	{
		foreach (get_declared_classes() as $class) {
			if (is_subclass_of($class, 'Controller'))
			{
				$this->Controllers[$class] = new $class($this->framework);
			}
		}
	}

	public function offsetExists($controller)
	{
		return isset($this->Controllers[$controller]) || isset($this->Controllers[$controller . 'Controller']);
			
	}

	public function offsetGet($controller)
	{
		if(isset($this->Controllers[$controller]))
		{
			return $this->Controllers[$controller];
		}
		if(isset($this->Controllers[$controller . 'Controller']))
		{
			return $this->Controllers[$controller . 'Controller'];
		}
		throw new Exception('No such controller:' . $controller);
	}

	public function offsetSet($offset, $value)
	{
		
	}

	public function offsetUnset($offset)
	{
		
	}
}