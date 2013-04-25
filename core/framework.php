<?php

class Framework
{
	private $core = array();
	private $plugin = array();
	private static $instance;
	public $Options;
	
	public static function GetInstance()
	{
		return self::$instance;
	}

	public function __construct($options = array())
	{
		$this->instance = $this;
		$this->Options = array(
			'MainDir' => '.' . DS . 'app',
			'TemplateDir' => 'templates',
			'CompiledTemplateDir' => 'compiledtemplates',
			'ControllerDir' => 'controllers',
			'ModelDir' => 'models',
			'PluginDir' => 'plugins',
			'DBConnections' => array()
		);
		if(is_array($options))
		{
			$this->Options = array_merge($this->Options, array_intersect_key($options, $this->Options));
		}
		foreach(get_declared_classes() as $class)
		{
			if(in_array("CoreInit", class_implements($class)))
			{
				$this->core[$class] = new $class($this);
			}
		}
		foreach($this->core as $core)
		{
			$core->Init();
		}
		if(($plugindir = @opendir($this->Options['MainDir'] . DS . $this->Options['PluginDir'])) !== false) {
			while (($plugin = readdir($plugindir)) !== false) 
			{
				if($plugin == '.' || $plugin == '..')
				{
					continue;
				}
				if(is_dir($this->Options['MainDir'] . DS . $this->Options['PluginDir'] . DS . $plugin) && file_exists($this->Options['MainDir'] . DS . $this->Options['PluginDir'] . DS . $plugin . DS . 'init.php'))
				{
					include_once $this->Options['MainDir'] . DS . $this->Options['PluginDir'] . DS . $plugin . DS . 'init.php';
				}
			}
			closedir($plugindir);
		}
		foreach(get_declared_classes() as $class)
		{
			if(in_array("PluginInit", class_implements($class)))
			{
				$this->plugin[$class] = new $class($this);
			}
		}
		foreach($this->plugin as $plugin)
		{
			$plugin->Init();
		}
	}

	public function &__get($module)
	{
		if(isset($this->core[$module]))
		{
			return $this->core[$module];
		}
		if(isset($this->plugin[$module]))
		{
			return $this->plugin[$module];
		}
		throw new Exception('Component ' . $module . ' doesn\'t exist');
	}

	public function __isset($module)
	{
		return isset($this->core[$module]) || isset($this->plugin[$module]);
	}

}