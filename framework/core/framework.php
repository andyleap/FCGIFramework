<?php

class Framework
{
	private $core = array();
	public $Options;

	public function __construct($options = array())
	{
		$this->Options = array(
			'MainDir' => '.' . DS . 'app',
			'TemplateDir' => 'templates',
			'CompiledTemplateDir' => 'compiledtemplates',
			'ControllerDir' => 'controllers',
			'ModelDir' => 'models',
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
	}

	public function &__get($core)
	{
		if(isset($this->core[$core]))
		{
			return $this->core[$core];
		}
		throw new Exception('Core component ' . $core . ' doesn\'t exist');
	}

	public function __isset($core)
	{
		return isset($this->core[$core]);
	}

}