<?php

class TemplateCache implements ArrayAccess, CoreInit
{
	private $templates = array();
	private $framework;

	function __construct($framework)
	{
		$this->framework = $framework;
		$templateDir = $this->framework->Options['MainDir'] . DS . $this->framework->Options['TemplateDir'];
		$dirs = array($templateDir);
		$templates = array();
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
							$templates[] = substr($dir . DS . $file, strlen($templateDir) + 1, -4);
						}
					}
				}
				closedir($dirHandle);
			}
		}
		foreach($templates as $template)
		{
			$this->templates[$template] = function($vars) use ($template, $templateDir)
					{
						extract($vars);
						include $templateDir . DS . $template . '.php';
					};
		}
	}

	public function Init()
	{
		
	}

	public function offsetExists($offset)
	{
		return isset($this->templates[$offset]);
	}

	public function offsetGet($offset)
	{
		if(isset($this->templates[$offset]))
		{
			return new Template($this->templates[$offset]);
		}
		else
		{
			throw new Exception('No such template');
		}
	}

	public function offsetSet($offset, $value)
	{
		
	}

	public function offsetUnset($offset)
	{
		
	}

}