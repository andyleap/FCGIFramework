<?php

class ModelLoader implements CoreInit
{
	private $framework;
	
	function __construct($framework)
	{
		$this->framework = $framework;
		$modelDir = $this->framework->Options['MainDir'] . DS . $this->framework->Options['ModelDir'];
		$dirs = array($modelDir);
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
		$config = ActiveRecord\Config::instance();
		$config->set_connections($this->framework->Options['DBConnections']);
		foreach (get_declared_classes() as $class) {
			if (is_subclass_of($class, 'Model'))
			{
				ActiveRecord\Table::load($class);
			}
		}
	}
}
